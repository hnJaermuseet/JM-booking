<?php

/**
 * Included by entry_list.php and entry_list_export_excel.php
 */

require 'glob_inc.inc.php';

filterMakeAlternatives();

if(!isset($_GET['listtype'])) {
    $listtype = '';
}
else {
    $listtype = $_GET['listtype'];
}
$addAdfterSQL = '';
$return_to = 'entry_list';
$entry_list_ingress = '';
switch($listtype)
{
	case 'not_confirmed':
		$entry_list_heading = __('Entries without confirmation sent');
		$entry_list_ingress = __('Entries in the past is not shown.').'<br><br>'.chr(10).chr(10);
		$filters = array();
		$filters = addFilter($filters, 'confirm_email', '0');
		$filters = addFilter($filters, 'time_start', 'current', '>');
		if($area != '')
			$filters = addFilter($filters, 'area_id', $area);
		$SQL = "select entry_id from `entry` where confirm_email = '0' and time_start > :time_start order by `time_start`";
		//$SQL = "select entry_id from `entry` where confirm_email = '0' order by `time_start`";
		break;

	case 'no_user_assigned':
		$entry_list_heading = __('Entries without any assigned user');
		$entry_list_ingress = __('Entries in the past is not shown.').'<br><br>'.chr(10).chr(10);
		$filters = array();
		$filters = addFilter($filters, 'user_assigned', '0');
		$filters = addFilter($filters, 'user_assigned2', '', 'is');
		$filters = addFilter($filters, 'time_start', 'current', '>');
		if($area != '')
			$filters = addFilter($filters, 'area_id', $area);
		//$SQL = "select entry_id from `entry` where user_assigned = ';0;' and user_assigned2 = '' and time_start > :time_start order by `time_start`";
		//$SQL = "select entry_id from `entry` where user_assigned = ';0;' and user_assigned2 = '' order by `time_start`";
		break;

	case 'next_100':
		$entry_list_heading = __('Next 100 entries');
		$entry_list_ingress = __('Entries in the past is not shown.').'<br><br>'.chr(10).chr(10);
		$filters = array();
		$filters = addFilter($filters, 'time_start', 'current', '>');
		if($area != '')
			$filters = addFilter($filters, 'area_id', $area);
		$addAdfterSQL = ' limit 100';
		$SQL = "select entry_id from `entry` where time_start > :time_start order by `time_start` limit 100";
		break;

	case 'servering':
		$entry_list_heading = 'Bookinger med servering fremover';
		$entry_list_ingress = __('Entries in the past is not shown.').'<br><br>'.chr(10).chr(10);
		$filters = array();
		$filters = addFilter($filters, 'time_start', 'current', '>');
		$filters = addFilter($filters, 'service_description', '_%');
		if($area != '')
			$filters = addFilter($filters, 'area_id', $area);
		$SQL = "select entry_id from `entry` where time_start > :time_start order by `time_start` limit 100";
		break;

	case 'customer_list':
		$entry_list_heading = 'Kundeliste';
		if(!isset($_GET['filters']))
			$_GET['filters'] = '';

		$filters = filterGetFromSerialized($_GET['filters']);
		if(!$filters)
			$filters = array();

		$return_to = 'customer_list';
		break;

	case 'deleted':
		$entry_list_heading = 'Slettede bookinger';
		if(!isset($_GET['filters']))
			$_GET['filters'] = '';

		$filters = filterGetFromSerialized($_GET['filters']);
		if(!$filters)
		{
			$filters = array();
			$filters = addFilter($filters, 'deleted', true);
		}

		break;

		$return_to = 'customer_list';
		break;

	default:
		$entry_list_heading = __('Entry list');
		if(!isset($_GET['filters']))
			$_GET['filters'] = '';

		$filters = filterGetFromSerialized($_GET['filters']);
		if(!$filters)
			$filters = array();

		$return_to = 'entry_list';
		break;
}

$SQL = genSQLFromFilters($filters, '*');
$SQL .= " order by `time_start`".$addAdfterSQL;

$Q = db()->prepare($SQL);
$Q->bindValue(':time_start', time(), PDO::PARAM_INT);
$Q->execute();