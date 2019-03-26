<?php

namespace TimeZoneOne\GDPR\Extension;

use TractorCow\DynamicCache\Extension\DynamicCacheExtension;

if(class_exists(DynamicCacheExtension::class)) {
    return;
}

class GDPRDynamicCachesExtension extends DynamicCacheExtension
{

	public function updateCacheKeyFragments(array &$fragments)
	{
		if(!DB::is_active()) {
			global $databaseConfig;
			if ($databaseConfig) {
				DB::connect($databaseConfig);
			}
		}
		$fragments['is_gdpr'] = SiteConfigGDPR::is_enable_for_request() ? 'YES' : 'NO';
	}

}
