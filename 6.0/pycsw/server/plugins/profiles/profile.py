# -*- coding: ISO-8859-15 -*-
# =================================================================
#
# $Id: profile.py 323 2011-09-27 14:27:57Z tomkralidis $
#
# Authors: Tom Kralidis <tomkralidis@hotmail.com>
#                Angelos Tzotsos <tzotsos@gmail.com>
#
# Copyright (c) 2011 Tom Kralidis
#
# Permission is hereby granted, free of charge, to any person
# obtaining a copy of this software and associated documentation
# files (the "Software"), to deal in the Software without
# restriction, including without limitation the rights to use,
# copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the
# Software is furnished to do so, subject to the following
# conditions:
#
# The above copyright notice and this permission notice shall be
# included in all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
# EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
# OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
# NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
# HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
# WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
# FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
# OTHER DEALINGS IN THE SOFTWARE.
#
# =================================================================

import os

class Profile(object):
    ''' base Profile class '''
    def __init__(self, name, version, title, url,
    namespace, typename, outputschema, prefixes, model, core_namespaces,
    added_namespaces,repository):

        ''' Initialize profile '''
        self.name = name
        self.version = version
        self.title = title
        self.url = url
        self.namespace = namespace
        self.typename = typename
        self.outputschema = outputschema
        self.prefixes = prefixes
        self.repository = repository

        model['operations']['DescribeRecord']['parameters']\
        ['typeName']['values'].append(self.typename)

        model['operations']['GetRecords']['parameters']['outputSchema']\
        ['values'].append(self.outputschema)

        model['operations']['GetRecords']['parameters']['typeNames']\
        ['values'].append(self.typename)

        model['operations']['GetRecordById']['parameters']['outputSchema']\
        ['values'].append(self.outputschema)

        if model['operations'].has_key('Harvest'):
            model['operations']['Harvest']['parameters']['ResourceType']\
            ['values'].append(self.outputschema)

        # namespaces
        core_namespaces.update(added_namespaces)

        # repository
        model['typenames'][self.typename] = self.repository

    def extend_core(self, model, namespaces, config):
        ''' Extend config.MODEL and config.NAMESPACES '''
        raise NotImplementedError

    def check_parameters(self):
        ''' Perform extra parameters checking.
            Return dict with keys "locator", "code", "text" or None ''' 
        raise NotImplementedError

    def get_extendedcapabilities(self):
        ''' Return ExtendedCapabilities child as lxml.etree.Element '''
        raise NotImplementedError

    def get_schemacomponents(self):
        ''' Return schema components as lxml.etree.Element list '''
        raise NotImplementedError
    
    def check_getdomain(self, kvp):
        '''Perform extra profile specific checks in the GetDomain request'''
        raise NotImplementedError

    def write_record(self, result, esn, outputschema, queryables):
        ''' Return csw:SearchResults child as lxml.etree.Element '''
        raise NotImplementedError

    def transform2dcmappings(self, queryables):
        ''' Transform information model mappings into csw:Record mappings ''' 
        raise NotImplementedError

def load_profiles(path, cls, profiles):
    ''' load CSW profiles, return dict by class name ''' 

    def look_for_subclass(modulename):
        module = __import__(modulename)
 
        dmod = module.__dict__
        for modname in modulename.split('.')[1:]:
            dmod = dmod[modname].__dict__
 
        for key, entry in dmod.items():
            if key == cls.__name__:
                continue
 
            try:
                if issubclass(entry, cls):
                    aps['plugins'][key] = entry
            except TypeError:
                continue
 
    aps = {}
    aps['plugins'] = {}
    aps['loaded'] = {}

    for prof in profiles.split(','):
        modulename='%s.%s.%s' % (path.replace(os.sep, '.'), prof, prof)
        look_for_subclass(modulename)
    return aps
