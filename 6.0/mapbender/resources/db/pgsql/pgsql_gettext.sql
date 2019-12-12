-- check if plpgsql exists; if yes, install it.
CREATE OR REPLACE FUNCTION public.create_plpgsql_language ()
        RETURNS TEXT
        AS $$
            CREATE LANGUAGE plpgsql;
            SELECT 'language plpgsql created'::TEXT;
        $$
LANGUAGE 'sql';

SELECT CASE WHEN
              (SELECT true::BOOLEAN
                 FROM pg_language
                WHERE lanname='plpgsql')
            THEN
              (SELECT 'language already installed'::TEXT)
            ELSE
              (SELECT public.create_plpgsql_language())
            END;

DROP FUNCTION public.create_plpgsql_language ();

-- function gettext for i18n (requires plpgsql)
CREATE FUNCTION gettext(locale_arg text, string text) RETURNS character varying
    AS $$
 DECLARE
    msgstr varchar(512);
    trl RECORD;
 BEGIN
    -- RAISE NOTICE '>%<', locale_arg;

    SELECT INTO trl * FROM translations
    WHERE trim(from locale) = trim(from locale_arg) AND msgid = string;
    -- we return the original string, if no translation is found.
    -- this is consistent with gettext's behaviour
    IF NOT FOUND THEN
        RETURN string;
    ELSE
        RETURN trl.msgstr;
    END IF; 
 END;
 $$
    LANGUAGE plpgsql;

