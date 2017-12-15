<?php

/*
JM-booking
Copyright (C) 2007-2010  Jaermuseet <http://www.jaermuseet.no>
Contact: <hn@jaermuseet.no> 
Project: <http://github.com/hnJaermuseet/JM-booking>

Based on ARBS, Advanced Resource Booking System, copyright (C) 2005-2007 
ITMC der TU Dortmund <http://sourceforge.net/projects/arbs/>. ARBS is based 
on MRBS by Daniel Gardner <http://mrbs.sourceforge.net/>.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/*
	Administration of attachements
*/

$section = 'attachment';

include "include/admin_top.php";

/* View attachment */
if(isset($_GET['att_id']))
{
	$att = getAttachment($_GET['att_id'], true);
	if(count($att))
	{
		if(isset($_POST['connection_program']))
		{
			$conProgram = getProgram($_POST['connection_program']);
			if(!count($conProgram))
			{
				include "include/admin_middel.php";
				templateError('Fant ikke programmet du pr�vde � koble til vedlegg nr '.$att['att_id']);
				exit();
			}

            $Q = db()->prepare("
				INSERT INTO `programs_defaultattachment`
				(`program_id`, `att_id`) VALUES
				(:program_id, :att_id);
				");
            $Q->bindValue(':program_id', $conProgram['program_id'], PDO::PARAM_INT);
            $Q->bindValue(':att_id', $att['att_id'], PDO::PARAM_INT);
            $Q->execute();
			
			if(isset($_GET['redirect']) && $_GET['redirect'] == 'program')
				header('Location: admin_programs.php?program_id='.$conProgram['program_id']);
			else
				header('Location: '.$_SERVER['PHP_SELF'].'?att_id='.$att['att_id']);
			exit();
		}
		
		if(isset($_POST['connection_entry_type']) && isset($_POST['connection_area']))
		{
			$conEntryType = getEntryType($_POST['connection_entry_type']);
			if(!count($conEntryType))
			{
				include "include/admin_middel.php";
				templateError('Fant ikke bookingtypen du pr�vde � koble til vedlegg nr '.$att['att_id']);
				exit();
			}
			
			$conArea = getArea($_POST['connection_area']);
			if(!count($conArea))
			{
				include "include/admin_middel.php";
				templateError('Fant ikke anlegget du pr�vde � koble til vedlegg nr '.$att['att_id']);
				exit();
			}


            $Q = db()->prepare("
				INSERT INTO `entry_type_defaultattachment`
				(
					`entry_type_id`, 
					`area_id`, 
					`att_id`
				) VALUES (
					:entry_type_id,
					:area_id,
					:att_id
				);
				");
            $Q->bindValue(':entry_type_id', $conEntryType['entry_type_id'], PDO::PARAM_INT);
            $Q->bindValue(':area_id' ,$conArea['area_id'], PDO::PARAM_INT);
            $Q->bindValue(':att_id', $att['att_id'], PDO::PARAM_INT);
            $Q->execute();
			
			if(isset($_GET['redirect']) && $_GET['redirect'] == 'entry_type') {
                header('Location: admin_entry_type.php?entry_type_id=' . $conEntryType['entry_type_id']);
            }
			else {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?att_id=' . $att['att_id']);
            }
			exit();
		}
		
		
		include "include/admin_middel.php";
		
		$entry_types = array();
		$Q_entry_type = db()->prepare("
			SELECT
				entry_type.entry_type_id as id, 
				entry_type.entry_type_name as name 
			FROM `entry_type`
			ORDER BY name");
        $Q_entry_type->execute();
		while($R = $Q_entry_type->fetch()) {
			$entry_types[$R['id']] = $R['name'];
		}
		$programs = array();
		$Q_programs = db()->prepare("
			SELECT
				programs.program_id as id, 
				CONCAT(mrbs_area.area_name, ' - ', programs.program_name) as name 
			FROM `programs` LEFT JOIN `mrbs_area` 
				ON programs.area_id = mrbs_area.id
			ORDER BY name");
        $Q_programs->execute();
		while($R = $Q_programs->fetch()) {
			$programs[$R['id']] = $R['name'];
		}
		$areas = array();
		$Q_areas = db()->prepare("SELECT id, area_name as name FROM `mrbs_area` ORDER BY name");
        $Q_areas->execute();
		while($R = $Q_areas->fetch()) {
			$areas[$R['id']] = $R['name'];
		}
		
		$smarty = new Smarty();
		templateAssignSystemvars('smarty');
		$smarty->assign('att', $att);
		$smarty->assign('entry_types', $entry_types);
		$smarty->assign('programs', $programs);
		$smarty->assign('areas', $areas);
		$smarty->assign('entry_confirm_att_path', $entry_confirm_att_path);
		$smarty->display('admin-attachment-view.tpl');
		
		exit();
	}
	else
	{
		include "include/admin_middel.php";
		templateError('Finner ikke vedlegget');
		exit();
	}
}

/* Fileupload */
$feilmelding = '';
if(isset($_FILES['file']))
{
	if (isset($_FILES['file']['error']) && $_FILES['file']['error'] > 0)
	{
		switch($_FILES['file']['error'])
		{
			case UPLOAD_ERR_OK:
					echo 'Error: 0; There is no error, the file uploaded with success.'; break;
			case UPLOAD_ERR_INI_SIZE:
					echo 'Error: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.'; break;
			case UPLOAD_ERR_FORM_SIZE:
					echo 'Error: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.'; break;
			case UPLOAD_ERR_PARTIAL:
					echo 'Error: 3; The uploaded file was only partially uploaded.'; break;
			case UPLOAD_ERR_NO_FILE:
					echo 'Error: 4; No file was uploaded.'; break;
			case UPLOAD_ERR_NO_TMP_DIR:
					echo 'Error: 6; Missing a temporary folder.'; break;
			case UPLOAD_ERR_CANT_WRITE:
					echo 'Error: 7; Failed to write file to disk.'; break;
			case UPLOAD_ERR_EXTENSION:
					echo 'Error: 8; File upload stopped by extension.'; break;
			default:
					echo 'Error: '.$_FILES['file']['error'].': Unknown error.'; break;
		}
		exit();
	}
	else
	{
		switch($_FILES["file"]["type"])
		{
			case 'application/envoy':
			case 'application/fractals':
			case 'application/futuresplash':
			case 'application/hta':
			case 'application/internet-property-stream':
			case 'application/mac-binhex40':
			case 'application/msword':
			case 'application/octet-stream':
			case 'application/oda':
			case 'application/olescript':
			case 'application/pdf':
			case 'application/pics-rules':
			case 'application/pkcs10':
			case 'application/pkix-crl':
			case 'application/postscript':
			case 'application/rtf':
			case 'application/set-payment-initiation':
			case 'application/set-registration-initiation':
			case 'application/vnd.ms-excel':
			case 'application/vnd.ms-outlook':
			case 'application/vnd.ms-pkicertstore':
			case 'application/vnd.ms-pkiseccat':
			case 'application/vnd.ms-pkistl':
			case 'application/vnd.ms-powerpoint':
			case 'application/vnd.ms-project':
			case 'application/vnd.ms-works':
			case 'application/winhlp':
			case 'application/x-bcpio':
			case 'application/x-cdf':
			case 'application/x-compress':
			case 'application/x-compressed':
			case 'application/x-cpio':
			case 'application/x-csh':
			case 'application/x-director':
			case 'application/x-dvi':
			case 'application/x-gtar':
			case 'application/x-gzip':
			case 'application/x-hdf':
			case 'application/x-internet-signup':
			case 'application/x-iphone':
			case 'application/x-javascript':
			case 'application/x-latex':
			case 'application/x-msaccess':
			case 'application/x-mscardfile':
			case 'application/x-msclip':
			case 'application/x-msdownload':
			case 'application/x-msmediaview':
			case 'application/x-msmetafile':
			case 'application/x-msmoney':
			case 'application/x-mspublisher':
			case 'application/x-msschedule':
			case 'application/x-msterminal':
			case 'application/x-mswrite':
			case 'application/x-netcdf':
			case 'application/x-perfmon':
			case 'application/x-pkcs12':
			case 'application/x-pkcs12':
			case 'application/x-pkcs7-certificates':
			case 'application/x-pkcs7-certificates':
			case 'application/x-pkcs7-certreqresp':
			case 'application/x-pkcs7-mime':
			case 'application/x-pkcs7-mime':
			case 'application/x-pkcs7-signature':
			case 'application/x-sh':
			case 'application/x-shar':
			case 'application/x-shockwave-flash':
			case 'application/x-stuffit':
			case 'application/x-sv4cpio':
			case 'application/x-sv4crc':
			case 'application/x-tar':
			case 'application/x-tcl':
			case 'application/x-tex':
			case 'application/x-texinfo':
			case 'application/x-texinfo':
			case 'application/x-troff':
			case 'application/x-troff-man':
			case 'application/x-troff-me':
			case 'application/x-troff-ms':
			case 'application/x-ustar':
			case 'application/x-wais-source':
			case 'application/x-x509-ca-cert':
			case 'application/ynd.ms-pkipko':
			case 'application/zip':
			case 'audio/basic':
			case 'audio/mid':
			case 'audio/mpeg':
			case 'audio/x-aiff':
			case 'audio/x-mpegurl':
			case 'audio/x-pn-realaudio':
			case 'audio/x-pn-realaudio':
			case 'audio/x-wav':
			case 'image/bmp':
			case 'image/cis-cod':
			case 'image/gif':
			case 'image/ief':
			case 'image/jpeg':
			case 'image/pipeg':
			case 'image/svg+xml':
			case 'image/tiff':
			case 'image/tiff':
			case 'image/x-cmu-raster':
			case 'image/x-cmx':
			case 'image/x-icon':
			case 'image/x-portable-anymap':
			case 'image/x-portable-bitmap':
			case 'image/x-portable-graymap':
			case 'image/x-portable-pixmap':
			case 'image/x-rgb':
			case 'image/x-xbitmap':
			case 'image/x-xpixmap':
			case 'image/x-xwindowdump':
			case 'message/rfc822':
			case 'text/css':
			case 'text/h323':
			case 'text/html':
			case 'text/iuls':
			case 'text/plain':
			case 'text/richtext':
			case 'text/scriptlet':
			case 'text/tab-separated-values':
			case 'text/webviewhtml':
			case 'text/x-component':
			case 'text/x-setext':
			case 'text/x-vcard':
			case 'video/mpeg':
			case 'video/quicktime':
			case 'video/x-la-asf':
			case 'video/x-ms-asf':
			case 'video/x-msvideo':
			case 'video/x-sgi-movie':
			case 'x-world/x-vrml':
			case 'application/dia':
					$att_filetype = $_FILES['file']['type'];
				break;
			default:
				$feilmelding = 'Filtypen er ikke st�ttet.<br>';
				$feilmelding .= 'Filtype: '.$_FILES['file']['type'];
				break;
		}
		
		if($feilmelding == '')
		{
			$user_id = $_SESSION['user_id'];
			$att_uploadtime = time();
			$att_filesize = $_FILES['file']['size'];
			$att_filename_orig = str_replace(
				array('�','�', '�', '�', '�', '�'),
				array('aa','AA', 'oe', 'OE', 'ae', 'AE'),
				$_FILES['file']['name']);
			$att_filename = date('Ymd_His').'_'.$att_filename_orig;
			move_uploaded_file($_FILES['file']['tmp_name'], $entry_confirm_att_path.'/'.$att_filename);
			
			// Insert MySQL
			$Q = db()->prepare("
				INSERT INTO `entry_confirm_attachment` 
					( 
						`att_filetype` ,
						`att_filename` , 
						`att_filename_orig` , 
						`att_filesize` , 
						`att_uploadtime` , 
						`user_id`
					) 
					VALUES (
						:att_filetype,
						:att_filename,
						:att_filename_orig,
						:att_filesize,
						:att_uploadtime,
						:user_id
						)
				;");
            $Q->bindValue(':att_filetype', $att_filetype, PDO::PARAM_STR);
            $Q->bindValue(':att_filename', $att_filename, PDO::PARAM_STR);
            $Q->bindValue(':att_filename_orig', $att_filename_orig, PDO::PARAM_STR);
            $Q->bindValue(':att_filesize', $att_filesize, PDO::PARAM_STR);
            $Q->bindValue(':att_uploadtime', $att_uploadtime, PDO::PARAM_INT);
            $Q->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $Q->execute();
            header("Location: admin_attachment.php?att_id=" . db()->lastInsertId());
            exit();
		}
	}
}

include "include/admin_middel.php";

/* Getting the attachments */
$attachments_lastused = array();
$Q_att = db()->prepare("
	SELECT att.att_id
	FROM `entry_confirm_attachment` att 
	LEFT JOIN `entry_confirm_usedatt` used 
	ON att.att_id = used.att_id  
	GROUP BY att.att_id
	ORDER BY att.att_id desc LIMIT 10");
$Q_att->execute();
while($R_att = $Q_att->fetch())
{
	$att = getAttachment($R_att['att_id'], true);
	if(count($att)) {
        $attachments_lastused[] = $att;
    }
}
$attachments_lastupload = array();
$Q_att = db()->prepare("
	SELECT att.att_id
	FROM `entry_confirm_attachment` att
	ORDER BY att.att_uploadtime desc LIMIT 10");
$Q_att->execute();
while($R_att = $Q_att->fetch())
{
	$att = getAttachment($R_att['att_id'], true);
	if(count($att)) {
        $attachments_lastupload[] = $att;
    }
}

if(isset($_GET['viewall']))
{
	$attachments = array();
	$Q_att = db()->prepare("
		SELECT att.att_id
		FROM `entry_confirm_attachment` att
		ORDER BY att.att_filename_orig");
    $Q_att->execute();
	while($R_att = $Q_att->fetch())
	{
		$att = getAttachment($R_att['att_id'], true);
		if(count($att)) {
            $attachments[] = $att;
        }
		else
		{
			echo 'err';
			exit();
		}
	}
}


$smarty = new Smarty();
templateAssignSystemvars('smarty');
$smarty->assign('feilmelding', $feilmelding);
if(isset($_GET['viewall'])) {
	$smarty->assign('attachments', $attachments);
	$smarty->assign('viewall', true);
}
else
{
	$smarty->assign('attachments_lastused', $attachments_lastused);
	$smarty->assign('attachments_lastupload', $attachments_lastupload);
	$smarty->assign('viewall', false);
}
$smarty->assign('entry_confirm_att_path', $entry_confirm_att_path);
$smarty->display('admin-attachment-list.tpl');
