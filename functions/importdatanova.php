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

function datanova_webreport_login($baseurl, $username, $password, $shop, &$ch, $relogin = false)
{
    $url = $baseurl . '/';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        '__EVENTARGUMENT=&' .
        '__EVENTTARGET=&' .
        '__EVENTVALIDATION=%2FwEWBgKD%2BameBALerNSyAQKG87HkBgK1qbSRCwLCi9reAwKT%2BPmaCOggkRMmzvBRUaODy9GRtkyi%2FxHG&' .
        '__LASTFOCUS=&' .
        '__VIEWSTATE=%2FwEPDwUKLTY2Nzg3NjgzNw9kFgICAw9kFgQCAw8PFgIeBFRleHQFAk9LZGQCBA8PFgIfAAUHQXZzbHV0dGRkZF7uMzjbgEjAEfvEtDliGVIPXb8X&' .
        'ucUserLogin$btnLogin.x=14&' .
        'ucUserLogin$btnLogin.y=16&' .
        'ucUserLogin$txtUID=' . $username .'&' .
        'ucUserLogin$txtPWD=' . $password .'&');
    $result = curl_exec($ch);

    $wrong_usernameorpassword = '<script type=\'text/javascript\'>alert(\'Feil brukernavn og/eller passord\')</script></form>';
    $login_failedorsomething_message = '<span id="lblMessage"><font face="Arial Narrow" color="Red" size="5">Vennligst legg inn riktig informasjon</font></span>';

    if (strpos($result, $wrong_usernameorpassword) !== FALSE) {
        throw new Exception ('Login failed for Datanova Web reports. Username or password might be wrong. ' .
            'Please check with a login to ' . $baseurl . ' in a browser and check the configuration.');
    }
}

/*
function datanova_webreport_logout ($baseurl, &$ch)
{
	$url = $baseurl.'/default.aspx?flag=logout';
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_POST, 0);
	$result = curl_exec ($ch);
}
*/

function datanova_webreport_getreport($baseurl, $username, $password, $shop, $year, $varegruppe_fra, $varegruppe_til)
{
    /* Init */
    $cookie = 'datanova_webreport.txt';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);

    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    /* Login */
    datanova_webreport_login($baseurl, $username, $password, $shop, $ch);


    /* Generate report */
    $url = $baseurl . '/MainReport.aspx?nodeid=6.4';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 0);
    $result = curl_exec($ch);

    // Get viewstate and eventvalidation (needed in this ASP.NET application to verify the origin of the form)
    preg_match_all("#<input.*?name=\"__viewstate\".*?value=\"(.*?)\".*?>.*?<input.*?name=\"__viewstategenerator\".*?value=\"(.*?)\".*?>#mis", $result, $arr_viewstate);
    if (!isset($arr_viewstate[1][0]) || !isset($arr_viewstate[2][0])) {
        throw new Exception ('Viewstate/Eventvalidation not found in result from dnrepparam.aspx in first request after login. HTML fetching failed.' .
            chr(10) . 'HTML body: ' . chr(10) . $result);
    }
    $viewstate = $arr_viewstate[1][0];
    $eventvalidation = $arr_viewstate[2][0];

    // Set up post data
    $post = array(
            /*
        'B11[]=1-Garborgsenteret', 'B11[]=3-Vitenfabrikken', 'B11[]=4-Vitengarden', 'B11[]=5-Garborgstova',
        'D1=' . $year, 'D2=' . $year,
        'P13=' . $varegruppe_fra, 'P14=' . $varegruppe_til,

        'D10=', 'D21=', 'D22=', 'D23=', 'D24=', 'D25=', 'D26=', 'D27=', 'D28=', 'D29=', 'D3=', 'D30=', 'D4=', 'D5=', 'D6=', 'D9=',
        'DropDownListGraphic=Sylinder',
        'Emailsubmit=',
        'Grid1_Data=',
        'Grid1_EventList=',
        'H8=8',
        'ImageButton13.x=48',
        'ImageButton13.y=13',
        'L7=', 'L8=',
        'P15=', 'P16=', 'P17=', 'P18=', 'P19=', 'P20=', 'T18=1',
        '__EVENTARGUMENT=',
        '__EVENTTARGET=',
        // Example - eventvalidation:
        //'__EVENTVALIDATION=%2FwEW3gICtMGshQECkuD%2F5AgC3O%2Fm7QwCn%2FixrA0Cn%2FiN8QYC9MG%2F6AEC9MGLjQkC9MGn5A8C9MGziQcC9MGP0ggC9MGb9wEC9MH3mwkC9MHDvAIC9MHfwQsC9MGr6gwCsKLCnAkCsKLeoQICsKLqmAcCsKLGvQgCsKLSxgECsKKu6woCsKK6jAICsKKW0QsCsKLi9QwCsKL%2BngQCjYvgsQcCjYv82ggCjYuIsg0CjYvk1gYCjYvw%2Bw8CjYvMnAcCjYvYoQgCjYu0ygECjYuA7woCjYucsAICuuGg9wgCuuG8GAK64cjzBgK64aSUDgK64bC5BwK64YzCCAK64ZjnAQK64fSLCQK64cCsAgK64dzxCwKXys7oBgKXytqNDgKXyvbkDAKXysKJBAKXyt7SDQKXyqr3BgKXyoaYDgKXypK9BwKXyu7BCAKXyvrqAQLs0%2BydAwLs0%2FimBALs05SeCQLs0%2BCiAgLs0%2FzHCwLs08joDALs06SNBALs07DWDQLs04z7BgLs05icDgLc7%2BLtDAKf%2BLWsDQKf%2BInxBgL0wbvoAQL0wY%2BNCQL0waPkDwL0wbeJBwL0wYvSCAL0wZ%2F3AQL0wfObCQL0wce8AgL0wdvBCwL0wa%2FqDAKwosacCQKwotqhAgKwou6YBwKwosK9CAKwotbGAQKwoqrrCgKwor6MAgKwopLRCwKwoub1DAKwovqeBAKNi%2BSxBwKNi%2FjaCAKNi4yyDQKNi%2BDWBgKNi%2FT7DwKNi8icBwKNi9yhCAKNi7DKAQKNi4TvCgKNi5iwAgK64aT3CAK64bgYArrhzPMGArrhoJQOArrhtLkHArrhiMIIArrhnOcBArrh8IsJArrhxKwCArrh2PELApfKyugGApfK3o0OApfK8uQMApfKxokEApfK2tINApfKrvcGApfKgpgOApfKlr0HApfK6sEIApfK%2FuoBAuzT6J0DAuzT%2FKYEAuzTkJ4JAuzT5KICAuzT%2BMcLAuzTzOgMAuzToI0EAuzTtNYNAuzTiPsGAuzTnJwOAtzv3u0MAtOA9AMC0oD0AwLRgPQDAtCA9AMC14D0AwLWgPQDAtWA9AMCxID0AwLLgPQDAtOANALTgDgC04A8Atzv2u0MAtOA8AMC0oDwAwLRgPADAtCA8AMC14DwAwLWgPADAtWA8AMCxIDwAwLLgPADAtOAMALTgDwC04A4Atzv1u0MAtOA%2FAMC0oD8AwLRgPwDAtCA%2FAMC14D8AwLWgPwDAtWA%2FAMCxID8AwLLgPwDAtOAPALTgDAC04A0AtOACALTgAwCUwLTgAQC04AYAtOA3AMC04DQAwLSgDwC0oAwAtKANALSgAgC0oAMAlIC0oAEAtKAGALSgNwDAtKA0AMC0YA8AtGAMALRgDQC0YAIAtGADAJRAtGABALRgBgC0YDcAwLRgNADAtCAPALQgDAC0IA0AtCACALQgAwCUALQgAQC0IAYAtCA3AMC0IDQAwLXgDwC14AwAteANALXgAgC3O%2FS7QwC04D4AwLSgPgDAtGA%2BAMC0ID4AwLXgPgDAtaA%2BAMC1YD4AwLEgPgDAsuA%2BAMC04A4AtOANALTgDAC04AMAtOACALTgAQCUwLTgBwC04DYAwLTgNQDAtKAOALSgDQC0oAwAtKADALSgAgC0oAEAlIC0oAcAtKA2AMC0oDUAwLRgDgC0YA0AtGAMALRgAwC0YAIAtGABAJRAtGAHALRgNgDAtGA1AMC0IA4AtCANALQgDAC0IAMAtCACALQgAQCUALQgBwC0IDYAwLQgNQDAteAOALXgDQC14AwAteADALk787tDALk74ruDALc74buDALMgCwC04AsAtGALALQgCwC14DsAwL2rom6CwLmwaPUBwL5waPUBwL7waPUBwL6waPUBwL9wePXBwKaz8j9DALNt9iuBgKsjr4iApi%2Fr%2FkBApLrlMEDAtvq4vkJAvbTgI8EApG9nqQOAqymvLkIAseP2s4CArLlmpAKAs3OuKUEAoqvhboLApGYo88FAqyBweQPAsfq3vkJAuLT%2FI4EAv28mqQOApimuLkIArOP1s4CAp7llpAKArnOtKUEAvaugboLAoztgtQGArKM4aYMAqHCgpEHAsKoi5kMApSHz4sOAqPA4dYJAujviu4MAq7lmpAKAszzsqwNAvrl05MGAvWVpqAKAord6dACAoWxguoPAqrh0LQKAtLCmdMIAo%2BlvtIOAteu16YJAv2P844PAreHjOEEArPervEKAoypjxICj6mPEgKOqY8SAvb4%2BJQEAqS8%2BNIKAomTpZ4FAua8h6EPAsOlmbQBAoCFyqEKAv2urMsNAvDDqskKAoutyN4EArrx7p4GAtXajDQC0efw4woCk%2B2Uyw8CxI%2FhlQ0C8PPPwgoCpbGOUALy8vSpDALOrNveAgK1v8L3BgLp3uL5BAKQmLX2BwKRlq1qArnCucoFAs7t4tAHApmEiscOAv%2FExeMKAoiE2LoMApbf880B3WPWF1S6LV%2B9lTTVS2Xe9cunLVM%3D',
        '__EVENTVALIDATION=' . $eventvalidation,
        '__LASTFOCUS=',
        // Example - viewstate:
        //'__VIEWSTATE=%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%2BPC9MZXZlbHM%2BZGQCAw8PFgYfDGYfDQURPExldmVscz48L0xldmVscz4fDmZkZAILD2QWBgIJDxAPZBYCHgVzdHlsZQURdmlzaWJpbGl0eTpoaWRkZW5kZGQCFw8PZBYCHwQFHmphdmFzY3JpcHQ6cmV0dXJuIHNhdmVFbWFpbCgpO2QCGQ8PFgIfAwUQRW1haWwgYW5tZWxkZWxzZRYCHwQFIGphdmFzY3JpcHQ6cmV0dXJuIG9wRW1haWxQYWdlKCk7ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WFwUDQjExBQVjdGwyNgUMSW1hZ2VCdXR0b24xBQ1JbWFnZUJ1dHRvbjExBQ1JbWFnZUJ1dHRvbjEyBQ1JbWFnZUJ1dHRvbjEzBQ5iYWNrdXBvbnNlcnZlcgUPUmFkaW9CdXR0b25GcmUxBQ9SYWRpb0J1dHRvbkZyZTEFD1JhZGlvQnV0dG9uRnJlMgUPUmFkaW9CdXR0b25GcmUyBQ9SYWRpb0J1dHRvbkZyZTMFD1JhZGlvQnV0dG9uRnJlMwUPUmFkaW9CdXR0b25GcmU0BQ9SYWRpb0J1dHRvbkZyZTQFD1JhZGlvQnV0dG9uRnJlNQUPUmFkaW9CdXR0b25GcmU1BQ9DaGVja0JveExpc3QxJDAFD0NoZWNrQm94TGlzdDEkMQUPQ2hlY2tCb3hMaXN0MSQyBQ9DaGVja0JveExpc3QxJDMFD0NoZWNrQm94TGlzdDEkMwUMYnRuRW1haWxTYXZlK9T2aYh4y%2FBHZuSBJqMp460JClU%3D',
        '__VIEWSTATE=' . $viewstate,
        'ctl22=sum(GrossSalesVal)',
        'ctl24=t.copkShopNo%2B' - '%2Bt.coShopName',
        'ctl26=on',
        'hdnAlert1=',
        'hdnEmail=',
        'hdnExePath=',
        'hdnFileName=',
        'hdnGraphField=1',
        'hdnGraphOption=',
        'hdnLanguage=NOR',
        'hdnMQry=select%20%20coshopno%2C(select%20coshopname%20from%20tashop%20where%20copkshopno%3Dcoshopno)%20as%20coshopname%2C%20coitemname%2Ccoitemno%2CN_cofkitemgroupno%2CCodate%2Csalesqty%2CMRP%2CGrossSalesVal%2Ccodiscamt%2CCoPuramt%2CTurnoverExDisc%2Ccosaleswovat%2Cvatamt%2CcoActivitySalesAmt%2CcoCredSalesAmt%2Ccogrossprofitamt%2CGrossProfitPercent%2CBudGrossProfitPercent%2CVariance%2CCoWeekno%2CCoSupplierNo%2C(select%20t1.cosupitemno%20from%20talinkitem_supplier%20t1%20join%20talinkitem_profile%20t2%20%20on%20%20copfksupprofileno%3Dcopfkprofileno%20and%20t1.copfksupplierno%3Dt2.cofkactivesupplierno%20and%20t1.copfkitemno%3Dt2.copfkitemno%20%20where%20t1.copfkitemno%3DViDnRepItemDailySales_FSM.coItemNo%20and%20copfksupprofileno%3Ddbo.profile())%20as%20suppitemno%2Ccocolourname%20as%20Farge%2C%20CONVERT(VARCHAR(30)%2Ccosizename)%20as%20~St%C3%B8rrelse~%2C%20comodelname%20as%20Modell%2C(select%20cofkmanufno%20from%20taelectrical%20where%20copfkitemno%20%3Dcoitemno)%20as%20%20copkmanufno%20%20from%20%20ViDnRepItemDailySales_FSM%20%20where%20coyear%3E%3D\'2011\'%20and%20%20coyear%3C%3D\'2011\'%20and%20%20coshopno%20in%20(1%2C3%2C4%2C5)%20and%20%20n_cofkitemgroupno%3E%3D\'' . $varegruppe_fra . '\'%20and%20%20n_cofkitemgroupno%3C%3D\'' . $varegruppe_til . '\'%20order%20by%20N_cofkitemgroupno%2CCoitemname%2CCodate',
        'hdnMemberId=1003',
        'hdnPreview=0',
        'hdnReportName=6.04%20Salg%20pr.%20vare%20pr.%20dag%20(%20spesifisert%20)',
        'hdnSavePath=',
        'hdnTab=0',
        'hdnTotaliseFieldValues=0~0~0~0~0~0~28342.000~0~1067890.00~1831.00~0.00~1066059.00~1011152.14~54906.86~0.00~0.00~403416.64~39.90~39.14~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0~0',
        'hdnYaxisvalue=Butikk',
        'hdnYvalue=',
        'hdnbackupserver=',
        'hdnfieldnames=Butikk%20nr.%2CButikk%20navn%2CVarenavn%2CVarenr.%2CVaregruppenr.%2CTrans.-dato%2CAnt.solgt%2CS.pris%2CBruttooms.%2CRabatt%20bel%C3%B8p%2CInnkj%C3%B8ps-bel%C3%B8p%2COms.etter%20rab.%2COms.%20u%2Fmva%2Cmvabel%C3%B8p%2CKamp.salg%2CKredittsalg%2CBruttofortj.%20kr.%2CBruttooms.fortj.%20%25%2CBud.%20bto.fortj.%20%25%2CAvvik%2CUkenr.%2CLev.nr.%2CLev.Varenr.%2C',
        'hidChildName=6.4',
        'hidField=',
        'searchVal=',
        */

        '__EVENTTARGET=ctl00$Content$ucParam$btnSend',
        '__EVENTARGUMENT=',
        '__VIEWSTATE=' . $viewstate,
        '__VIEWSTATEGENERATOR=9B61073C',
        'ctl00$ucLoggedIn$hdnUserid=' . $username,
        'ctl00$Content$txtSearch=Søk',
        'ctl00$Content$ucParam$selddl1=' . $year,
        'ctl00$Content$ucParam$dt71=',
        'ctl00$Content$ucParam$dt81=',
        // Garborgsenteret
        'ctl00$Content$ucParam$selddl11=1',
        // Vitenfabrikken
        'ctl00$Content$ucParam$selddl11=3',
        // Vitengarden
        'ctl00$Content$ucParam$selddl11=4',
        // Garborgstova
        'ctl00$Content$ucParam$selddl11=5',
        'ctl00$Content$ucParam$txt131=' . $varegruppe_fra,
        'ctl00$Content$ucParam$txt141=' . $varegruppe_til,
        'ctl00$Content$ucParam$txt151=',
        'ctl00$Content$ucParam$txt161=',
        'ctl00$Content$ucParam$txt171=',
        'ctl00$Content$ucParam$txt181=',
        'ctl00$Content$ucParam$txt191=',
        'ctl00$Content$ucParam$txt201=',
        'ctl00$Content$ucParam$txt341=',
        'ctl00$Content$ucParam$txt351=',
        'ctl00$Content$ucParam$ddlXaxis=coshopno',
        'ctl00$Content$ucParam$ddlYaxis=salesqty',
        'ctl00$Content$ucParam$ddlGraph=Bar',
        'ctl00$Content$ucParam$hdnTextId=',
        'ctl00$Content$ucParam$hdnNodeid=6.4',
        'ctl00$Content$ucParam$hdnstock=',
        'ctl00$Content$ucParam$hdnXaxis=',
        'ctl00$Content$ucParam$hdnYaxis=',
        'ctl00$Content$ucParam$hdnGraphType=',
        'ctl00$Content$ucParam$hdnorderby=0',
        'ctl00$Content$ucParam$hdnsort=',
    );

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $result = curl_exec($ch);

    //$info = curl_getinfo($ch);
    //echo '<h3>'.nl2br($info['request_header']).'</h3>';
    //echo '<div style="width: 500px; height: 250px; display: inline-block; clear: none; overflow: scroll;">'.$result.'</div><br>';

    // Extract viewstate and eventvalidation
    preg_match_all("#<input.*?name=\"__viewstate\".*?value=\"(.*?)\".*?>.*?<input.*?name=\"__viewstategenerator\".*?value=\"(.*?)\".*?>.*?<input.*?name=\"__eventvalidation\".*?value=\"(.*?)\".*?>#mis", $result, $arr_viewstate);
    $viewstate = $arr_viewstate[1][0];
    $viewstategenerator = $arr_viewstate[2][0];
    $eventvalidation = $arr_viewstate[3][0];

    /* Download report */
    $url = $baseurl . '/ShowReport.aspx?childid=6.4&childtext=6.04+Salg+pr.+vare+pr.+dag+(+spesifisert+)';

    $post = array(
        '__EVENTTARGET=ctl00$Content$ucParam$btnXmlExport',
        '__EVENTARGUMENT=Click',
        'ctl00$ucLoggedIn$hdnUserid=' . $username,
        'ctl00$Content$ucParam$grid$DXSelInput=',
        'ctl00$Content$ucParam$grid$CallbackState=asdfasdfasdf',
        'ctl00$Content$ucParam$grid$DXColResizedInput=',
        'ctl00$Content$ucParam$grid$DXSyncInput=',
        'ctl00$Content$ucParam$hdnGroupBy=',
        'ctl00$Content$ucParam$hdnGroupBy2=',
        'ctl00$Content$ucParam$hdnHiddenCol1=',
        'ctl00$Content$ucParam$hdnHiddenCol2=',
        'ctl00$Content$ucShowGraph$ddlXaxis=coshopno',
        'ctl00$Content$ucShowGraph$ddlYaxis=salesqty',
        'ctl00$Content$ucShowGraph$hdnGraphOption=bar',
        'ctl00$Content$ucShowGraph$hdnPrev=1',
        'ctl00$Content$ucShowGraph$hdnNext=1',
        'ctl00$Content$ucShowGraph$hdnNodeId=6.4',
        'ctl00$Content$ucShowGraphLine$ddlXaxis=coshopno',
        'ctl00$Content$ucShowGraphLine$ddlYaxis=salesqty',
        'ctl00$Content$ucShowGraphLine$hdnGraphOption=line',
        'ctl00$Content$ucShowGraphLine$hdnPrev=1',
        'ctl00$Content$ucShowGraphLine$hdnNext=1',
        'ctl00$Content$ucShowGraphLine$hdnNodeId=6.4',
        'ctl00$Content$ucShowGraphArea$ddlXaxis=coshopno',
        'ctl00$Content$ucShowGraphArea$ddlYaxis=salesqty',
        'ctl00$Content$ucShowGraphArea$hdnGraphOption=area',
        'ctl00$Content$ucShowGraphArea$hdnPrev=1',
        'ctl00$Content$ucShowGraphArea$hdnNext=1',
        'ctl00$Content$ucShowGraphArea$hdnNodeId=6.4',
        'ctl00$Content$ucShowGraphPie$ddlXaxis=coshopno',
        'ctl00$Content$ucShowGraphPie$ddlYaxis=salesqty',
        'ctl00$Content$ucShowGraphPie$hdnGraphOption=pie',
        'ctl00$Content$ucShowGraphPie$hdnPrev=1',
        'ctl00$Content$ucShowGraphPie$hdnNext=1',
        'ctl00$Content$ucShowGraphPie$hdnNodeId=6.4',
        'ctl00$Content$hdnReportId=6.4',
        'ctl00$Content$ucDownload$Emailsubmit=',
        'ctl00$Content$ucDownload$hdnMemberId=' . $username
    );

    $validations = array(
        '__VIEWSTATE=' . $viewstate,
        '__VIEWSTATEGENERATOR=' . $viewstategenerator.
        '__EVENTVALIDATION=',
    );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $post) . '&' . http_build_query($validations));
    $result = curl_exec($ch);

    //$info = curl_getinfo($ch);
    //echo '<h3>'.nl2br($info['request_header']).'</h3>';
    //echo implode('&', $post).'&'.http_build_query($validations).'&'.implode('&', $post_last).chr(10).chr(10);

    /* Clean up */
    curl_close($ch);

    /* Return HTML (+ headers) */
    return $result;
}

function str_starts_with($content, $needle) {
    return substr($content, 0, strlen($needle)) == $needle;
}

/**
 * Parse HTML retrived from Datanova Back office web reporter
 *
 * @param String  The whole HTML (can include header etc)
 * @return Array  Data found (array with 'Butikknr', 'Varenavn', 'Varenr', 'Antsolgt' and 'Transdato')
 */
function datanova_webreport_parser($result, $current_date_dd_mm_yyyy)
{
    $result = utf8_decode($result);
    $lines = explode("\n", $result);
    if (!str_starts_with(trim(substr($lines[0], 1)), '<?xml version="1.0" encoding="utf-8"')) {
        echo '---------'.chr(10);
        var_dump($lines[0]);
        echo '---------'.chr(10);
        throw new Exception('Unknown result. Did not return XML: '.chr(10) .$result);
    }


    if (!str_starts_with($lines[1], '<ivxml version="1.0.0" createdate="'. $current_date_dd_mm_yyyy . '" delimiter="|" parameters="')) {
        throw new Exception('Unknown result. Unknown line 2: '.chr(10) .$result);
    }

    $a = '<report name="6.04 Salg pr. vare pr. dag ( spesifisert )" allrows="true" columns="';
    if (!str_starts_with(trim($lines[2]), $a)) {
        throw new Exception('Unknown result. Unknown line 3: '.chr(10) .$result);
    }

    $columns = substr(trim($lines[2]), strlen($a));
    $columns = substr($columns, 0, strlen($columns)-2);
    $columns = explode('|', $columns);

    $heading = array();
    $data = array();

    foreach($columns as $i => $column) {
        // Cleaning
        $td = $column;
        $td = trim(str_replace('.', ' ', $td));
        $td = trim(str_replace('-', ' ', $td));
        $td = str_replace('  ', ' ', $td);
        //$td = str_replace(' ', '_', $td);
        $td = str_replace(' ', '', $td);
        $td = str_replace('%', 'prosent', $td);
        $td = str_replace('ø', 'o', $td);

        // Translate to old column names (not make the rest of the logic work)
        if ($td == 'Antallsolgt') {
            $td = 'Antsolgt';
        }
        if ($td == 'Dato') {
            $td = 'Transdato';
        }

        // Adding to list
        if (
            // Ignoring a lot for fields
            $td == 'Butikknr' ||
            $td == 'Varenavn' ||
            $td == 'Varenr' ||
            $td == 'Antsolgt' ||
            $td == 'Transdato'
        ) {
            $heading[$i] = $td;
        }
        else {
            //echo 'Ignoring '. $i .': '.$td.chr(10);
        }
    }

    //var_dump($heading);

    // Getting table content
    $controlamount = 0;
    $a = 0;
    // Iterate over the rows (not the first lines and not the 2 last lines)
    for ($j = 3; $j < count($lines) - 2; $j++) {
        $line = explode('|',
            trim(
                str_replace('<row values="', '',
                str_replace('" />', '', $lines[$j]))
            ));
        $row = array();
        foreach ($line as $i => $item) {
            // Cleaning / fixing
            $td = $item;
            $td = str_replace('å', '�', $td);
            $td = str_replace('æ', '�', $td);
            $td = str_replace('ø', '�', $td);
            $td = str_replace('Å', '�', $td);

            // Adding to array
            if (
                // A lot for fields are ignored
            isset($heading[$i])
            ) {
                if ($heading[$i] == 'Antsolgt') {
                    $td = (int)$td;
                    if ($row['Butikknr'] != 'Totalt :')
                        $controlamount += $td;
                }

                if ($heading[$i] == 'Transdato' && $td != '') {
                    // 6/7/2016 12:00:00 AM => 12.03.2016
                    // M/D/YYYY ----------- => dd.mm.YYYY
                    $td = explode(' ', $td);
                    $td = explode('/', $td[0]);
                    if (count($td) != 3) {
                        throw new Exception('Unknown date: ' . $item);
                    }
                    $td = str_pad($td[1], 2, '0', STR_PAD_LEFT)
                        . '.' . str_pad($td[0], 2, '0', STR_PAD_LEFT)
                        . '.' . $td[2];
                }

                $row[$heading[$i]] = $td;
            }
        }

        $data[$a] = $row;
        $a++;
    }

    if ($data[$a - 1]['Butikknr'] != 'Totalt :') {
        throw new Exception('Faulty read of table or bogus data.. No sum found.');
    }

    if ($data[$a - 1]['Antsolgt'] != $controlamount) {
        throw new Exception('Faulty read of table or bogus data. Control amount is not correct. Could also have been sold half a ticket. Should be: ' . $data[$a - 1]['Antsolgt'] . '. Was: ' . $controlamount);
    }

    unset($data[$a - 1]);

    /*
    echo '<table>'.chr(10);
    echo '<tr>'.chr(10);
    foreach($heading as $cell)
    {
        echo '	<th>'.$cell.'</th>'.chr(10);
    }
    echo '</tr>'.chr(10).chr(10);

    foreach($data as $row)
    {
        echo '<tr>'.chr(10);
        foreach($row as $cell)
        {
            echo '	<td>'.$cell.'</td>'.chr(10);
        }
        echo '</tr>'.chr(10).chr(10);
    }
    echo '</table>';
    */

    return $data;
}

/**
 * Analyze the Datanova data
 *
 * Format $data_rows:
 * array(
 *      'Butikknr'  => '3',
 *      'Varenavn'  => 'ABC',
 *      'Varenr'    => '123321123',
 *      'Antsolgt'  => '123',
 *      'Transdato' => '12.03.4567'
 * )
 *
 * @param  Array  Rows of data
 * @param  Array  Shop-area translation (shop_id => area_id)
 * @return Array  Final analyzed data in different categories
 */
function datanova_analyze_data($data_rows, $shops)
{
    $Q_varer = mysql_query("SELECT varereg.*, kat.kat_navn AS kat_navn
	FROM import_dn_vareregister varereg LEFT JOIN import_dn_kategori kat
	ON varereg.kat_id = kat.kat_id
	");
    //where varereg.area_id = '$area';");
    $areavarer = array(); // vare_nr => array()
    while ($R_vare = mysql_fetch_assoc($Q_varer))
        $areavarer[$R_vare['area_id'] . '_' . $R_vare['vare_nr']] = $R_vare;

    $unknowns = array();
    $found = array();
    $tall_nye = array();
    $tall_update = array();
    $tall_ignore = array();
    $tall_ignore2 = array();
    $tall_allerede = array();
    $varer_nye = array();
    $varer_update = array();
    foreach ($data_rows as $key => $val) {
        $vare = array();
        $vare['vare_nr'] = slashes(htmlspecialchars($val['Varenr'], ENT_QUOTES));
        $vare['vare_navn'] = slashes(htmlspecialchars($val['Varenavn'], ENT_QUOTES));
        $vare['vare_antall'] = (int)$val['Antsolgt'];
        if (strlen($val['Transdato']) != strlen('11.06.2008')) {
            throw new Exception('Problemer med tolking av dato. Dato er ikke i rett format for ' . $vare['vare_nr'] . ' (dato: ' . $val['Transdato'] . '). Vare: ' . print_r($vare, true));
        } else {
            $vare['dag'] = getTime($val['Transdato'], array('d', 'm', 'y'));
            if ($vare['dag'] == 0)
                throw new Exception('Problemer med tolking av dato. Dato er ikke i rett format for ' . $vare['vare_nr'] . ' (dato: ' . $val['Transdato'] . '). Vare: ' . print_r($vare, true));
        }

        $vare['shop_id'] = $val['Butikknr'];
        if (isset($shops[$vare['shop_id']])) {
            $vare['area_id'] = $shops[$vare['shop_id']];
        } else {
            $vare['area_id'] = 0;
        }

        $vare_id_primary = $vare['area_id'] . '_' . $vare['vare_nr'];

        /* Determine import */
        if (!isset($areavarer[$vare_id_primary])) {
            if (!isset($unknowns[$vare_id_primary])) {
                $unknowns[$vare_id_primary] = $vare;
                unset($unknowns[$vare_id_primary]['dag']);
                $unknowns[$vare_id_primary]['vare_dager'] = 1;
            } else {
                $unknowns[$vare_id_primary]['vare_antall'] += $vare['vare_antall'];
                $unknowns[$vare_id_primary]['vare_dager'] += 1;
            }
            $tall_ignore[] = $vare;
        } else {
            // Varer funnet
            if (!isset($found[$vare_id_primary])) {
                $found[$vare_id_primary] = $vare;
                unset($found[$vare_id_primary]['dag']);
                $found[$vare_id_primary]['vare_dager'] = 1;
            } else {
                $found[$vare_id_primary]['vare_antall'] += $vare['vare_antall'];
                $found[$vare_id_primary]['vare_dager'] += 1;
            }

            $vare_med_kat = $areavarer[$vare_id_primary];
            $vare['kat_id'] = $areavarer[$vare_id_primary]['kat_id'];
            if ($areavarer[$vare_id_primary]['barn'] == 0) {
                $vare['antall_barn'] = 0;
                $vare['antall_voksne'] = $vare['vare_antall'];
            } else {
                $vare['antall_barn'] = $vare['vare_antall'];
                $vare['antall_voksne'] = 0;
            }

            if ($vare_med_kat['kat_id'] == 0) {
                $tall_ignore2[] = $vare;
            } else {
                // Sjekker mot database
                $Q_dbsjekk = mysql_query("SELECT * FROM `import_dn_tall` WHERE
					vare_nr = '" . $vare['vare_nr'] . "' AND
					area_id = '" . $vare['area_id'] . "' AND
					dag = '" . $vare['dag'] . "'
					LIMIT 1;");
                if (!mysql_num_rows($Q_dbsjekk)) {
                    $tall_nye[] = $vare;

                    // Nye varer
                    if (!isset($varer_nye[$vare_id_primary])) {
                        $varer_nye[$vare_id_primary] = $vare;
                        unset($varer_nye[$vare_id_primary]['dag']);
                        $varer_nye[$vare_id_primary]['vare_dager'] = 1;
                    } else {
                        $varer_nye[$vare_id_primary]['vare_antall'] += $vare['vare_antall'];
                        $varer_nye[$vare_id_primary]['vare_dager'] += 1;
                    }
                } else {
                    $tall = mysql_fetch_assoc($Q_dbsjekk);
                    if (
                        $tall['kat_id'] != $vare['kat_id'] ||
                        $tall['antall_barn'] != $vare['antall_barn'] ||
                        $tall['antall_voksne'] != $vare['antall_voksne']
                    ) {
                        $tall_update[] = $vare;

                        // Update av varer
                        if (!isset($varer_update[$vare_id_primary])) {
                            $varer_update[$vare_id_primary] = $vare;
                            unset($varer_update[$vare_id_primary]['dag']);
                            $varer_update[$vare_id_primary]['vare_dager'] = 1;
                        } else {
                            $varer_update[$vare_id_primary]['vare_antall'] += $vare['vare_antall'];
                            $varer_update[$vare_id_primary]['vare_dager'] += 1;
                        }
                    } else
                        $tall_allerede[] = $vare;
                }
            }
        }
    }

    return array(
        'unknowns' => $unknowns,
        'numbers_new' => $tall_nye,
        'numbers_update' => $tall_update,
        'numbers_ignored_notreged' => $tall_ignore,
        'numbers_ignored_reged' => $tall_ignore2,
        'numbers_alreadyimported' => $tall_allerede,
    );
}

/**
 * Insert new numbers in database
 *
 * @throws Exception on MySQL errors
 * @param  array   New numbers to be inserted
 * @return int     Amount of new numbers inserted
 */
function datanova_databaseinsert($tall_nye)
{
    if (count($tall_nye)) {
        $tall_nye2 = array();
        foreach ($tall_nye as $vare) {
            // Insert
            $tall_nye2[] =
                '\'' . $vare['vare_nr'] . '\',' .
                '\'' . $vare['area_id'] . '\',' .
                '\'' . $vare['dag'] . '\',' .
                '\'' . $vare['kat_id'] . '\',' .
                '\'' . $vare['antall_barn'] . '\',' .
                '\'' . $vare['antall_voksne'] . '\',' .
                '\'' . $vare['shop_id'] . '\'';
        }


        // Make batches of 100 and 100 inserts
        $tall_nye3 = array(0 => array());
        $i = 0;
        foreach ($tall_nye2 as $tall) {
            if (count($tall_nye3[$i]) >= 100) {
                $i++;
                $tall_nye3[$i] = array();
            }
            $tall_nye3[$i][] = $tall;
        }

        $total_count = 0;
        foreach ($tall_nye3 as $tall_nye4) {
            if (count($tall_nye4)) {
                // -> There are numbers in this batch
                $total_count += count($tall_nye4);

                $query =
                    'INSERT INTO `import_dn_tall` (
						`vare_nr`,
						`area_id`,
						`dag`,
						`kat_id`,
						`antall_barn`,
						`antall_voksne`,
						`shop_id`
					) VALUES (' . implode('),(', $tall_nye4) . ');';
                mysql_query($query);

                if (mysql_error()) {
                    throw new Exception(
                        'MySQL error when inserting new numbers to database: ' .
                        mysql_error() . '. ' .
                        'Query: ' . $query);
                }
            }
        }

        // Make sure the splitting has gone okey
        if (count($tall_nye2) != $total_count) {
            throw new Exception('$tall_nye2 (' . $tall_nye2 . ') is not the same as $total_count (' . $total_count . ')');
        }

        return $total_count;
    } else {
        return 0;
    }
}

function datanova_databaseupdate($numbers_update)
{
    if (count($numbers_update)) {
        foreach ($numbers_update as $vare) {
            mysql_query("DELETE FROM `import_dn_tall` WHERE
					`vare_nr` = '" . $vare['vare_nr'] . "' AND
					`shop_id` = '" . $vare['shop_id'] . "' AND
					`dag`     = '" . $vare['dag'] . "'
				LIMIT 1;");

            if (mysql_error()) {
                throw new Exception('MySQL error when deleting numbers (' . print_r($vare, true) . ') from database: ' . mysql_error());
            }
        }

        return datanova_databaseinsert($numbers_update);
    } else {
        return 0;
    }
}

function datanova_databaseupdate_notimported($numbers_update_notimported)
{
    if (count($numbers_update_notimported)) {
        $tall_nye2 = array();
        foreach ($numbers_update_notimported as $vare) {
            mysql_query("DELETE FROM `import_dn_tall_ikkeimportert` WHERE
					`vare_nr` = '" . $vare['vare_nr'] . "' AND
					`shop_id` = '" . $vare['shop_id'] . "'
				LIMIT 1;");

            if (mysql_error()) {
                throw new Exception('MySQL error when deleting not-imported numbers (' . print_r($vare, true) . ') from database: ' . mysql_error());
            }

            // Insert
            $tall_nye2[] =
                '\'' . $vare['vare_nr'] . '\',' .
                '\'' . $vare['shop_id'] . '\',' .
                '\'' . $vare['vare_navn'] . '\',' .
                '\'' . $vare['vare_antall'] . '\',' .
                '\'' . $vare['area_id'] . '\',' .
                '\'' . $vare['vare_dager'] . '\'';
        }

        mysql_query('INSERT INTO `import_dn_tall_ikkeimportert` (
			`vare_nr`,
			`shop_id`,
			`vare_navn`,
			`vare_antall`,
			`area_id`,
			`vare_dager`
		) VALUES (' . implode('),(', $tall_nye2) . ');');

        if (mysql_error()) {
            throw new Exception('MySQL error when inserting not-imported data to database: ' . mysql_error());
        } else {
            return count($tall_nye2);
        }
    } else {
        return 0;
    }
}