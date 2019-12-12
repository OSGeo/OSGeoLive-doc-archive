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

import org.mozilla.javascript.Scriptable;

/**
 * This class implements a "proxy" object for use in the JavaScript
 * execution environment provided by the Rhino interpreter.
 *
 * Why?  This object is used to provide a stable reference when, in
 * reality, we want to be able to destroy and replace the object that
 * the reference appears to provide.  For example, there is a
 * JavaScript class "A", and you have a factory method that provides
 * instances of "A".  However, there are behaviors in the system that
 * wish to restore an instance of "A" to its original, pristine state,
 * after it has already been created.  This proxy object is intended
 * to solve situations of that type.
 * 
 * To implement this example using Proxy: The factory, while still
 * creating an instance of "A" (which we'll call "A1"), also creates
 * and instance of Proxy ("P1") and sets P1's "prototype" to point to
 * A1.  This ensures that all attempts to execute a method of A1, or
 * to look up a variable it contains, will "pass through" the Proxy P1
 * via normal JS inheritance.  However, creation of new members of A1
 * has to be handled specially by the Proxy.  Each attempt to put a
 * new member to P1 will be explicitly passed to A1.  Subsequent
 * attempts to reference (or to change the value of) these new members
 * will then (again) access them through the prototype pointer and
 * inheritance.
 *
 * Finally, when it comes time to reinitialize A1, the function that
 * accomplishes it that has to be aware of the proxying arrangement.
 * It simply discards A1 altogether, creates a new instance ("A2"),
 * and reset's P1's "prototype" to refer to the new A2.  All other
 * code in the system which has a reference to P1 will still have a
 * valid references, but attempts to use A-class members will now be
 * directed to the new/fresh A2 instance.
 *
 */

public class Proxy extends org.mozilla.javascript.ScriptableObject
{

    public Proxy(){}
    public Proxy(Scriptable scope, Scriptable prototype){
        super(scope, prototype);
    }


    /* methods from the Scriptable interface that we let
     * ScriptableObject handle for us:
     *
     * get (both signatures)
     * getPrototype
     * setPrototype
     * hasInstance
     */


    /* The following methods are from the Scriptable interface.  We
     * implement by explicitly passing the calls on to the object
     * we're proxying. */

    public String getClassName(){
        return getPrototype().getClassName();
    }
    public boolean has(String name, Scriptable start){
        Scriptable proxysTarget = getPrototype();
        return proxysTarget.has(name, proxysTarget);
    }
    public boolean has(int index, Scriptable start){
        Scriptable proxysTarget = getPrototype();
        return proxysTarget.has(index, proxysTarget);
    }
    public void put(String name, Scriptable start, Object value){
        Scriptable proxysTarget = getPrototype();
        proxysTarget.put(name, proxysTarget, value);
    }
    public void put(int index, Scriptable start, Object value){
        Scriptable proxysTarget = getPrototype();
        proxysTarget.put(index, proxysTarget, value);
    }
    public void delete(String name){
        getPrototype().delete(name);
    }
    public void delete(int index){
        getPrototype().delete(index);
    }
    public Scriptable getParentScope(){
        return getPrototype().getParentScope();
    }
    public void setParentScope(Scriptable parent){
        getPrototype().setParentScope(parent);
    }
    public Object[] getIds(){
        return getPrototype().getIds();
    }
    public Object getDefaultValue(Class<?> typeHint){
        return getPrototype().getDefaultValue(typeHint);
    }
}
