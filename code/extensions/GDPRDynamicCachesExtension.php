<?php

/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 5/25/18
 * Time: 11:16 AM
 * To change this template use File | Settings | File Templates.
 */
class GDPRDynamicCachesExtension extends DataExtension
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