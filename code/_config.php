<?php


// GDPRDynamicCachesExtension
if(ClassInfo::exists('DynamicCache')) {
	Object::add_extension('DynamicCache', 'GDPRDynamicCachesExtension');
}