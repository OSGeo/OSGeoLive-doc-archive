/*
 * This file is a component of env.js, 
 *     http://github.com/gleneivey/env-js/commits/master/README
 * a Pure JavaScript Browser Environment
 * Copyright 2009 John Resig, licensed under the MIT License
 *     http://www.opensource.org/licenses/mit-license.php
 *
 *  Contributed by Glen E. Ivey
 */


package org.mozilla.javascript.tools.envjs;

import java.lang.reflect.Field;
import java.lang.reflect.Method;
import org.mozilla.javascript.Context;
import org.mozilla.javascript.NativeArray;


public class Global extends org.mozilla.javascript.tools.shell.Global
{

    public Global(org.mozilla.javascript.tools.shell.Global parentSharedScope)
    {
        this.setPrototype(parentSharedScope);
        this.setParentScope(null);
    }

    public void init(Context cx)
    {
        // we don't init, but make sure our parent is
        org.mozilla.javascript.tools.shell.Global uberGlobal = 
                (org.mozilla.javascript.tools.shell.Global) this.getPrototype();
        if (!uberGlobal.isInitialized())
            uberGlobal.init(cx);

        // shell.Global uses some package-scope instance variables, so...
        Class klass = org.mozilla.javascript.tools.shell.Global.class;

        try {

            // we don't want to run super.init(), but accesses to shell.Global's
            // history member don't go through JS' prototype chain, so we
            // initialize it "by hand"
            Field hist = klass.getDeclaredField("history");
            hist.setAccessible(true);
            if (hist.get(this) == null)
                hist.set(this, (NativeArray) cx.newArray(this, 0));

            // some users of Global access .initialized directly (bad
            // class, bad!)  so we set it in the base class rather
            // than over-riding isInitialized
            Field inited = klass.getDeclaredField("initialized");
            inited.setAccessible(true);
            inited.setBoolean(this, true);
        }
        catch (Exception ex){    // probably NoSuchField or IllegaAccess
            throw Context.reportRuntimeError(
                "Got a fatal exception when attempting to initialize " +
                "the shell.Global object.  Original error message is: " +
                ex.getMessage());
        }
    }
}
