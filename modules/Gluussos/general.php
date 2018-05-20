<?php

  /**
	 * @copyright Copyright (c) 2017, Gluu Inc. (https://gluu.org/)
	 * @license	  MIT   License            : <http://opensource.org/licenses/MIT>
	 *
	 * @package	  OpenID Connect SSO Module by Gluu
	 * @category  Module for SugarCrm
	 * @version   3.1.1
	 *
	 * @author    Gluu Inc.          : <https://gluu.org>
	 * @link      Oxd site           : <https://oxd.gluu.org>
	 * @link      Documentation      : <https://gluu.org/docs/oxd/3.0.1/plugin/sugarcrm/>
	 * @director  Mike Schwartz      : <mike@gluu.org>
	 * @support   Support email      : <support@gluu.org>
	 * @developer Volodya Karapetyan : <https://github.com/karapetyan88> <mr.karapetyan88@gmail.com>
	 *
	 *
	 * This content is released under the MIT License (MIT)
	 *
	 * Copyright (c) 2017, Gluu inc, USA, Austin
	 *
	 * Permission is hereby granted, free of charge, to any person obtaining a copy
	 * of this software and associated documentation files (the "Software"), to deal
	 * in the Software without restriction, including without limitation the rights
	 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	 * copies of the Software, and to permit persons to whom the Software is
	 * furnished to do so, subject to the following conditions:
	 *
	 * The above copyright notice and this permission notice shall be included in
	 * all copies or substantial portions of the Software.
	 *
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	 * THE SOFTWARE.
	 *
	 */
  
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
function getBaseUrl()
{
    $currentPath = $_SERVER['PHP_SELF'];
    $pathInfo = pathinfo($currentPath);
    $hostName = $_SERVER['HTTP_HOST'];
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    if (strpos($pathInfo['dirname'], '\\') !== false) {
        return $protocol . $hostName . "/";
    } else {
        return $protocol . $hostName . $pathInfo['dirname'] . "/";
    }
}
$base_url  = getBaseUrl();
$db = DBManagerFactory::getInstance();
$query = "CREATE TABLE IF NOT EXISTS `gluu_table` (
  `gluu_action` varchar(255) NOT NULL,
  `gluu_value` longtext NOT NULL,
  UNIQUE(`gluu_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$result = $db->query($query);
function select_query($db, $action){
    $result = $db->fetchRow($db->query("SELECT `gluu_value` FROM `gluu_table` WHERE `gluu_action` LIKE '$action'"))["gluu_value"];
    return $result;
}
function insert_query($db, $action, $value){
    $result = $db->query("INSERT INTO gluu_table (gluu_action, gluu_value) VALUES ('$action','$value')");
    return $result;
}
if(!select_query($db, 'gluu_scopes')){
    $get_scopes = json_encode(array("openid", "profile","email"));
    $result = insert_query($db, 'gluu_scopes', $get_scopes);
}
if(!select_query($db, 'gluu_acr')){
    $custom_scripts = json_encode(array('none'));
    $result = insert_query($db, 'gluu_acr', $custom_scripts);
}
if(!select_query($db, 'gluu_config')){
    $gluu_config = json_encode(array(
        "gluu_oxd_port" =>8099,
        "admin_email" => $GLOBALS['current_user']->email1,
        "authorization_redirect_uri" => $base_url.'gluu.php?gluu_login=Gluussos',
        "post_logout_redirect_uri" => $base_url.'gluu_logout.php?gluu_logout=Gluussos',
        "config_scopes" => ["openid","profile","email"],
        "gluu_client_id" => "",
        "gluu_client_secret" => "",
        "config_acr" => []
    ));
    $result = insert_query($db, 'gluu_config', $gluu_config);
}
if(!select_query($db, 'gluu_auth_type')){
    $gluu_auth_type = 'default';
    $result = insert_query($db, 'gluu_auth_type', $gluu_auth_type);
}
if(!select_query($db, 'gluu_custom_logout')){
    $gluu_custom_logout = '';
    $result = insert_query($db, 'gluu_custom_logout', $gluu_custom_logout);
}
if(!select_query($db, 'gluu_provider')){
    $gluu_provider = '';
    $result = insert_query($db, 'gluu_provider', $gluu_provider);
}
if(!select_query($db, 'gluu_send_user_check')){
    $gluu_send_user_check = 0;
    $result = insert_query($db, 'gluu_send_user_check', $gluu_send_user_check);
}
if(!select_query($db, 'gluu_oxd_id')){
    $gluu_oxd_id = '';
    $result = insert_query($db, 'gluu_oxd_id', $gluu_oxd_id);
}
if(!select_query($db, 'gluu_user_role')){
    $gluu_user_role = 0;
    $result = insert_query($db, 'gluu_user_role', $gluu_user_role);
}
if(!select_query($db, 'gluu_users_can_register')){
    $gluu_users_can_register = 1;
    $result = insert_query($db, 'gluu_users_can_register', $gluu_users_can_register);
}
if(!select_query($db, 'gluu_new_role')){
    $gluu_users_can_register = 1;
    $result = insert_query($db, 'gluu_new_role', null);
}
$get_scopes                 = json_decode(select_query($db, 'gluu_scopes'),true);
$gluu_config                = json_decode(select_query($db, 'gluu_config'),true);
$gluu_acr                   = json_decode(select_query($db, 'gluu_acr'),true);
$gluu_auth_type             = select_query($db, 'gluu_auth_type');
$gluu_send_user_check       = select_query($db, 'gluu_send_user_check');
$gluu_provider              = select_query($db, 'gluu_provider');
$gluu_user_role             = select_query($db, 'gluu_user_role');
$gluu_custom_logout         = select_query($db, 'gluu_custom_logout');
$gluu_new_roles              = json_decode(select_query($db, 'gluu_new_role'));
$gluu_users_can_register    = select_query($db, 'gluu_users_can_register');
$oxd_request_pattern = isset($gluu_config["oxd_request_pattern"])?$gluu_config["oxd_request_pattern"]:null;
//var_dump($oxd_request_pattern);
//var_dump($gluu_config);
//exit;
function gluu_is_oxd_registered(){
    $db = DBManagerFactory::getInstance();
    if(select_query($db, 'gluu_oxd_id')){
        $oxd_id = select_query($db, 'gluu_oxd_id');
        if(!$oxd_id ) {
            return 0;
        } else {
            return $oxd_id;
        }
    }
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="application/javascript">
    jQuery(document ).ready(function() {
        jQuery(document).ready(function() {
            
            <?php if($oxd_request_pattern == 1 || is_null($oxd_request_pattern)) { ?>
                jQuery(".port").show();
                jQuery(".host").hide();
            <?php } else if($oxd_request_pattern == 2) { ?>
                jQuery(".host").show();
                jQuery(".port").hide();
            <?php } ?>    
                
            jQuery("input[name='oxd_request_pattern']").change(function(){
                if(jQuery(this).val() == 1){
                    jQuery(".port").show();
                    jQuery(".host").hide();
                }else{
                    jQuery(".host").show();
                    jQuery(".port").hide();
                }
            });

            jQuery('[data-toggle="tooltip"]').tooltip();
            jQuery('#p_role').on('click', 'a.remrole', function() {
                jQuery(this).parents('.role_p').remove();
            });

        });
        
        <?php
        if($gluu_users_can_register == 2){
        ?>
                
        jQuery("#p_role").children().prop('disabled',false);
        jQuery("#p_role *").prop('disabled',false);
        
        <?php
        }else if($gluu_users_can_register == 3){
        ?>
        
        jQuery("#p_role").children().prop('disabled',true);
        jQuery("#p_role *").prop('disabled',true);
        jQuery("input[name='gluu_new_role[]']").each(function(){
            var striped = jQuery('#p_role');
            var value =  jQuery(this).attr("value");
            jQuery('<p><input type="hidden" name="gluu_new_role[]"  value= "'+value+'"/></p>').appendTo(striped);
        });
        jQuery("#UserType").prop('disabled',true);
        <?php
        }else{
        ?>
        jQuery("#p_role").children().prop('disabled',true);
        jQuery("#p_role *").prop('disabled',true);
        jQuery("input[name='gluu_new_role[]']").each(function(){
            var striped = jQuery('#p_role');
            var value =  jQuery(this).attr("value");
            jQuery('<p><input type="hidden" name="gluu_new_role[]"  value= "'+value+'"/></p>').appendTo(striped);
        });
        <?php
        }
        ?>
        jQuery('input:radio[name="gluu_users_can_register"]').change(function(){
            if(jQuery(this).is(':checked') && jQuery(this).val() == '2'){
                jQuery("#p_role").children().prop('disabled',false);
                jQuery("#p_role *").prop('disabled',false);
                jQuery("input[type='hidden'][name='gluu_new_role[]']").remove();
                jQuery("#UserType").prop('disabled',false);
            }else if(jQuery(this).is(':checked') && jQuery(this).val() == '3'){
                jQuery("#p_role").children().prop('disabled',true);
                jQuery("#p_role *").prop('disabled',true);
                jQuery("input[type='hidden'][name='gluu_new_role[]']").remove();
                jQuery("input[name='gluu_new_role[]']").each(function(){
                    var striped = jQuery('#p_role');
                    var value =  jQuery(this).attr("value");
                    jQuery('<p><input type="hidden" name="gluu_new_role[]"  value= "'+value+'"/></p>').appendTo(striped);
                });
                jQuery("#UserType").prop('disabled',true);
            }else{
                jQuery("#p_role").children().prop('disabled',true);
                jQuery("#p_role *").prop('disabled',true);
                jQuery("input[type='hidden'][name='gluu_new_role[]']").remove();
                jQuery("input[name='gluu_new_role[]']").each(function(){
                    var striped = jQuery('#p_role');
                    var value =  jQuery(this).attr("value");
                    jQuery('<p><input type="hidden" name="gluu_new_role[]"  value= "'+value+'"/></p>').appendTo(striped);
                });
                jQuery("#UserType").prop('disabled',false);
            }
        });
        jQuery("input[name='scope[]']").change(function(){
            var form=$("#scpe_update");
            if (jQuery(this).is(':checked')) {
                jQuery.ajax({
                    url: window.location,
                    type: 'POST',
                    data:form.serialize(),
                    success: function(result){
                        if(result){
                            return false;
                        }
                    }});
            }else{
                jQuery.ajax({
                    url: window.location,
                    type: 'POST',
                    data:form.serialize(),
                    success: function(result){
                        if(result){
                            return false;
                        }
                    }});
            }
        });
        jQuery('#p_role').on('click', '.remrole', function() {
            jQuery(this).parents('.role_p').remove();
        });
    });
</script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="modules/Gluussos/GluuOxd_Openid/css/gluu-oxd-css.css" rel="stylesheet"/>
<script src="modules/Gluussos/GluuOxd_Openid/js/scope-custom-script.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div class="container" style="width: 1200px;font-size: 13px !important;">
    <div class="row">
        <div class="col-md-12">
                <div id="messages">
                    <?php if (!empty($_SESSION['message_error'])){ ?>
                        <div class="mess_red_error" style="margin-bottom: 20px">
                            <?php echo $_SESSION['message_error']; ?>
                            <br/>
                        </div>
                        <?php unset($_SESSION['message_error']);} ?>
                    <?php if (!empty($_SESSION['message_success'])) { ?>
                        <div class="mess_green" style="margin-bottom: 20px">
                            <?php echo $_SESSION['message_success']; ?>
                            <br/>
                        </div>
                        <?php unset($_SESSION['message_success']);} ?>
                </div>
                <ul class="navbar navbar-tabs" style="margin-bottom: 0px; ">
                    <li class="active" id="account_setup"><a data-method="#accountsetup">General</a></li>
                    <?php if ( !gluu_is_oxd_registered()) {?>
                        <li id="social-sharing-setup"><a style="pointer-events: none; cursor: default;">OpenID Connect Configuration</a></li>
                    <?php }else {?>
                        <li id="social-sharing-setup"><a href="index.php?module=Gluussos&action=openidconfig">OpenID Connect Configuration</a></li>
                    <?php }?>
                    <li id=""><a data-method="#configopenid" href="https://gluu.org/docs/oxd/3.0.1/plugin/sugarcrm/" target="_blank">Documentation</a></li>
                </ul>
                <div class="container-page" style="padding: 0px !important;background-color: #e5fff3;">
                    <!-- General -->
                    <?php if (!gluu_is_oxd_registered()) { ?>
                        <!-- General tab-->
                        <div class="page" id="accountsetup">
                            <div class="mo2f_table_layout">
                                <form id="register_GluuOxd" name="f" method="post" action="index.php?module=Gluussos&action=gluuPostData">
                                    <input type="hidden" name="form_key" value="general_register_page"/>
                                    <fieldset style="border: 2px solid #53cc6b; padding: 20px">
                                    <legend style="border-bottom:none; width: 110px !important;">
                                        <img style=" height: 45px;" src="modules/Gluussos/GluuOxd_Openid/images/icons/gl.png"/>
                                    </legend>
                                    <div class="login_GluuOxd">
                                        <br/>
                                        <div style="padding-left:20px;">
                                            <p>The oxd OpenID Connect single sign-on (SSO) plugin for SugarCRM enables you to use a standard OpenID Connect Provider (OP), like Google or the Gluu Server, to authenticate and enroll users for your SugarCRM site.</p>
                                            <p>This plugin relies on the oxd mediator service. For oxd deployment instructions and license information, please visit the <a href="https://oxd.gluu.org/">oxd website</a>.</p>
                                            <p>In addition, if you want to host your own OP you can deploy the <a href="https://www.gluu.org/">free open source Gluu Server</a>.</p>
                                        </div>
                                        <div style="padding-left: 20px;">
                                            <h3 style="padding-left: 10px;padding-bottom: 20px; border-bottom: 2px solid black; width: 60% ">Server Settings</h3>
                                            <p style="margin-left:20px"><i>The below values are required to configure your SugarCRM site with your oxd server and OP. Upon successful registration of your SugarCRM site in the OP, a unique identifier will be issued and displayed below in a new field called: oxd ID.</i></p>
                                            <table class="table">
                                                <tr>
                                                    <td  style=" width: 40%"><b>URI of the OpenID Provider:</b></td>
                                                    <td><input class="" type="url" name="gluu_provider" id="gluu_provider"
                                                               autofocus="true"  placeholder="Enter URI of the OpenID Provider."
                                                               style="width:400px;"
                                                               value="<?php echo $gluu_provider;?>"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style=" width: 40%"><b>Custom URI after logout:</b></td>
                                                    <td><input class="" type="url" name="gluu_custom_logout" id="gluu_custom_logout"
                                                               autofocus="true"  placeholder="Enter custom URI after logout"
                                                               style="width:400px;"
                                                               value="<?php echo $gluu_custom_logout;?>"/>
                                                    </td>
                                                </tr>
                                                <?php if(!empty($_SESSION['openid_error'])){?>
                                                    <tr>
                                                        <td style=" width: 40%"><b><font color="#FF0000">*</font>Redirect URL:</b></td>
                                                        <td><input class="" type="url" name="gluu_redirect_url" id="gluu_redirect_url"
                                                                   autofocus="true" placeholder="Your redirect URL." disabled
                                                                   style="width:400px;"
                                                                   value="<?php echo $base_url.'gluu.php?gluu_login=Gluussos';?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" width: 40%"><b><font color="#FF0000">*</font>Client ID:</b></td>
                                                        <td><input class="" type="text" name="gluu_client_id" id="gluu_client_id"
                                                                   autofocus="true" placeholder="Enter OpenID Provider client ID."
                                                                   style="width:400px;"
                                                                   value="<?php if(!empty($gluu_config['gluu_client_id'])) echo $gluu_config['gluu_client_id']; ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" width: 40%"><b><font color="#FF0000">*</font>Client Secret:</b></td>
                                                        <td>
                                                            <input class="" type="text" name="gluu_client_secret" id="gluu_client_secret"
                                                                   autofocus="true" placeholder="Enter OpenID Provider client secret."  style="width:400px;" value="<?php if(!empty($gluu_config['gluu_client_secret'])) echo $gluu_config['gluu_client_secret']; ?>"/>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            <font color="#FF0000">*</font>Select oxd server / oxd https extension 
                                                            <a data-toggle="tooltip" class="tooltipLink" data-original-title="If you are using localhost to connect your SugarCRM site to your oxd server, choose oxd server. If you are connecting via https, choose oxd https extension.">
                                                                <span class="glyphicon glyphicon-info-sign"></span>
                                                            </a>
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-12">    
                                                                <div class="radio">
                                                                    <label><input type="radio" style="margin-top:1px" name="oxd_request_pattern" value="1" <?php if(empty($gluu_config['oxd_request_pattern']) || $gluu_config['oxd_request_pattern'] == 1) { echo "checked"; } ?> >oxd server</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="radio">
                                                                    <label><input type="radio" style="margin-top:1px" name="oxd_request_pattern" value="2"  <?php if(!empty($gluu_config['oxd_request_pattern']) && $gluu_config['oxd_request_pattern'] == 2) { echo "checked"; }; ?> >oxd https extension</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>    
                                                <tr class="port">
                                                    <td class="port" style=" width: 40%"><b><font color="#FF0000">*</font>oxd server port:</b></td>
                                                    <td class="port">
                                                        <input class="" type="number" name="gluu_oxd_port" min="0" max="65535"
                                                               value="<?php echo $gluu_config['gluu_oxd_port']; ?>"
                                                               style="width:400px;" placeholder="Please enter free port (for example 8099). (Min. number 0, Max. number 65535)."/>
                                                    </td>
                                                </tr>
                                                <tr class="host">
                                                    <td class="host"><b><font color="#FF0000">*</font>oxd https extension host:</b></td>
                                                    <td class="host">
                                                        <input type="text" style="width:400px;" value="<?php echo isset($gluu_config['gluu_oxd_host'])?$gluu_config['gluu_oxd_host']: ''; ?>" name="gluu_oxd_host" style="background-color: rgb(235, 235, 228);" placeholder="Please enter oxd https extension host">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div style="padding-left: 20px">
                                            <h3 style="padding-left: 10px;padding-bottom: 20px; border-bottom: 2px solid black; width: 60% ">Enrollment and Access Management
                                                <a data-toggle="tooltip" class="tooltipLink" data-original-title="Choose whether to register new users when they login at an external identity provider. If you disable automatic registration, new users will need to be manually created">
                                                    <span class="glyphicon glyphicon-info-sign"></span>
                                                </a>
                                            </h3>
                                            <div class="radio">
                                                <p><label><input name="gluu_users_can_register" type="radio" id="gluu_users_can_register" <?php if($gluu_users_can_register==1){ echo "checked";} ?> value="1" style="margin-right: 3px"><b> Automatically register any user with an account in the OpenID Provider</b></label></p>
                                            </div>
                                            <div class="radio">
                                                <p><label ><input name="gluu_users_can_register" type="radio" id="gluu_users_can_register_1" <?php if($gluu_users_can_register==2){ echo "checked";} ?> value="2" style="margin-right: 3px"><b> Only register and allow ongoing access to users with one or more of the following roles in the OpenID Provider</b></label></p>
                                                <div style="margin-left: 20px;">
                                                    <div id="p_role" >
                                                        <?php $k=0;
                                                        if(!empty($gluu_new_roles)) {
                                                            foreach ($gluu_new_roles as $gluu_new_role) {
                                                                if (!$k) {
                                                                    $k++;
                                                                    ?>
                                                                    <p class="role_p" style="padding-top: 10px">
                                                                        <input  type="text" name="gluu_new_role[]" class="form-control" style="display: inline"
                                                                                placeholder="Input role name"
                                                                                value="<?php echo $gluu_new_role; ?>"/>
                                                                        <button type="button" class="btn btn-xs add_new_role" onclick="test_add()"><span class="glyphicon glyphicon-plus"></span></button>
                                                                    </p>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <p class="role_p" style="padding-top: 10px">
                                                                        <input type="text" name="gluu_new_role[]" class="form-control" style="display: inline"
                                                                               placeholder="Input role name"
                                                                               value="<?php echo $gluu_new_role; ?>"/>
                                                                        <button type="button"  class="btn btn-xs add_new_role" onclick="test_add()"><span class="glyphicon glyphicon-plus"></span></button>
                                                                        <button type="button"  class="btn btn-xs remrole"><span class="glyphicon glyphicon-minus"></span></button>
                                                                    </p>
                                                                <?php }
                                                            }
                                                        }else{
                                                            ?>
                                                            <p class="role_p" style="padding-top: 10px">
                                                                <input type="text" name="gluu_new_role[]" class="form-control" placeholder="Input role name" style="display: inline" value=""/>
                                                                <button  type="button" class="btn btn-xs add_new_role" onclick="test_add()"><span class="glyphicon glyphicon-plus"></span></button>
                                                            </p>
                                                            <?php
                                                        }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="radio">
                                                <p>
                                                    <label >
                                                        <input name="gluu_users_can_register" type="radio" id="gluu_users_can_register_2" <?php if($gluu_users_can_register==3){ echo "checked";} ?> value="3" style="margin-right: 3px">
                                                        <b>Disable automatic registration</b>
                                                    </label>
                                                </p>
                                            </div>
                                            <table>
                                                <tr>
                                                    <td style="width: 54%;"><label for="UserType"><b>New User Default Role:</b></label></td>
                                                    <td>
                                                        <?php
                                                        $user_types = array(
                                                            array('name'=>'Regular User', 'status'=>'0'),
                                                            array('name'=>'System Administrator User', 'status'=>'1')
                                                        );
                                                        ?>
                                                        <div class="form-group" style="margin-bottom: 0px !important;">
                                                            <select id="UserType" class="form-control" name="gluu_user_role" >
                                                                <?php
                                                                foreach($user_types as $user_type){
                                                                    ?>
                                                                    <option <?php if($user_type['status'] == $gluu_user_role) echo 'selected'; ?> value="<?php echo $user_type['status'];?>"><?php echo $user_type['name'];?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="border-bottom:2px solid #000;"></div>
                                            <br/><br/>
                                            <?php if(!empty($_SESSION['openid_error'])){?>
                                                    <div class="row">
                                                        <div class="col-md-3 col-md-offset-3 text-right">
                                                            <div><input class="btn btn-primary" type="submit" name="register" value="Register" style="width: 120px;background-image:none;height: 35px;background-color: #337ab7;color:white!important;text-decoration:none !important;"/></div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <a class="btn btn-primary" onclick="return confirm('Are you sure that you want to remove this OpenID Connect provider? Users will no longer be able to authenticate against this OP.')" style="width: 120px;color:white!important;text-decoration:none !important;" href="index.php?module=Gluussos&action=gluuPostData&submit=delete">Delete</a>
                                                        </div>
                                                    </div>
                                                <?php }
                                                else{?>
                                                    <div class="row">
                                                        <?php if(!empty($gluu_provider)){?>
                                                            <div class="col-md-3 col-md-offset-3 text-right">
                                                                <input type="submit" style="width: 120px;background-image:none;height: 35px;background-color: #4e8ccf;color:white!important;text-decoration:none !important;" name="register" value="Register" class="btn btn-primary"/>
                                                            </div>
                                                            <div class="col-md-3 text-left">
                                                                <a class="btn btn-primary" onclick="return confirm('Are you sure that you want to remove this OpenID Connect provider? Users will no longer be able to authenticate against this OP.')"  style="width: 120px;background-image:none;height: 35px;background-color: #4e8ccf;color:white!important;text-decoration:none !important;" href="index.php?modazule=Gluussos&action=gluuPostData&submit=delete">Delete</a>
                                                            </div>
                                                        <?php }else{?>
                                                            <div class="col-md-4 col-md-offset-4 text-center">
                                                                <input type="submit" name="submit" value="Register" style="width: 120px;background-image:none;height: 35px;background-color: #4e8ccf;color:white!important;text-decoration:none !important;" class="btn btn-primary"/>
                                                            </div>
                                                        <?php }?>
                                                    </div>
                                                <?php }?>
                                        </div>
                                    </div>
                                    </fieldset>
                                    <br/>
                                </form>
                            </div>
                        </div>
                    <?php }
                    else{?>
                        <!-- General edit tab without client_id and client_secret -->
                        <div style="padding: 20px !important;" id="accountsetup">
                            <form id="register_GluuOxd" name="f" method="post" action="index.php?module=Gluussos&action=gluuPostData">
                                <input type="hidden" name="form_key" value="general_oxd_id_reset"/>
                                <fieldset style="border: 2px solid #53cc6b; padding: 20px">
                                    <legend style="border-bottom:none; width: 110px !important;">
                                        <img style=" height: 45px;" src="modules/Gluussos/GluuOxd_Openid/images/icons/gl.png"/>
                                    </legend>
                                    <div style="padding-left: 20px; margin-top: -30px;">
                                        <br/>
                                        <div style="padding-left:20px;">
                                            <p>The oxd OpenID Connect single sign-on (SSO) plugin for SugarCRM enables you to use a standard OpenID Connect Provider (OP), like Google or the Gluu Server, to authenticate and enroll users for your SugarCRM site.</p>
                                            <p>This plugin relies on the oxd mediator service. For oxd deployment instructions and license information, please visit the <a href="https://oxd.gluu.org/">oxd website</a>.</p>
                                            <p>In addition, if you want to host your own OP you can deploy the <a href="https://www.gluu.org/">free open source Gluu Server</a>.</p>
                                        </div>
                                        <div style="padding-left: 20px;">
                                            <h3 style="padding-left: 10px;padding-bottom: 20px; border-bottom: 2px solid black; width: 60% ">Server Settings</h3>
                                            <p style="margin-left:20px"><i>The below values are required to configure your SugarCRM site with your oxd server and OP. Upon successful registration of your SugarCRM site in the OP, a unique identifier will be issued and displayed below in a new field called: oxd ID.</i></p>
                                        <table class="table">
                                            <tr>
                                                <td  style=" width: 40%"><b>URI of the OpenID Provider:</b></td>
                                                <td><input type="url" name="gluu_provider" id="gluu_provider"
                                                           disabled placeholder="Enter URI of the OpenID Provider."
                                                           style="width:400px;background-color: rgb(235, 235, 228);"
                                                           value="<?php echo $gluu_provider; ?>"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style=" width: 40%"><b>Custom URI after logout:</b></td>
                                                <td><input class="" type="url" name="gluu_custom_logout" id="gluu_custom_logout"
                                                           autofocus="true" disabled placeholder="Enter custom URI after logout"
                                                           style="width:400px;background-color: rgb(235, 235, 228);"
                                                           value="<?php echo $gluu_custom_logout;?>"/>
                                                </td>
                                            </tr>
                                            <?php if(!empty($gluu_config['gluu_client_id']) and !empty($gluu_config['gluu_client_secret'])){?>
                                                <tr>
                                                    <td><b><font color="#FF0000">*</font>Redirect URL:</b></td>
                                                    <td><input class="" type="url" name="gluu_redirect_url" id="gluu_redirect_url"
                                                               autofocus="true" placeholder="Your redirect URL." disabled
                                                               style="width:400px;background-color: rgb(235, 235, 228);"
                                                               value="<?php echo $base_url.'gluu.php?gluu_login=Gluussos';?>"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b><font color="#FF0000">*</font>Client ID:</b></td>
                                                    <td><input class="" type="text" name="gluu_client_id" id="gluu_client_id"
                                                               autofocus="true" placeholder="Enter OpenID Provider client ID."
                                                               style="width:400px; background-color: rgb(235, 235, 228);" disabled
                                                               value="<?php if(!empty($gluu_config['gluu_client_id'])) echo $gluu_config['gluu_client_id']; ?>"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b><font color="#FF0000">*</font>Client Secret:</b></td>
                                                    <td>
                                                        <input class="" type="text" name="gluu_client_secret" id="gluu_client_secret" disabled
                                                               autofocus="true" placeholder="Enter OpenID Provider client secret."  style="width:400px; background-color: rgb(235, 235, 228);" value="<?php if(!empty($gluu_config['gluu_client_secret'])) echo $gluu_config['gluu_client_secret']; ?>"/>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        <font color="#FF0000">*</font>Select oxd server / oxd https extension 
                                                        <a data-toggle="tooltip" class="tooltipLink" data-original-title="If you are using localhost to connect your SugarCRM site to your oxd server, choose oxd server. If you are connecting via https, choose oxd https extension.">
                                                            <span class="glyphicon glyphicon-info-sign"></span>
                                                        </a>
                                                    </b>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-12">    
                                                            <div class="radio">
                                                                <label><input type="radio" style="margin-top:1px" disabled="" name="oxd_request_pattern" <?php if(empty($gluu_config['oxd_request_pattern']) || $gluu_config['oxd_request_pattern'] == 1) { echo "checked"; } ?> value="1">oxd server</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="radio">
                                                                <label><input type="radio" style="margin-top:1px" disabled="" name="oxd_request_pattern" <?php if(!empty($gluu_config['oxd_request_pattern']) && $gluu_config['oxd_request_pattern'] == 2) { echo "checked"; }; ?> value="2">oxd https extension</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="port">
                                                <td class="port" style=" width: 40%"><b><font color="#FF0000">*</font>oxd server port:</b></td>
                                                <td class="port">
                                                    <input class="" type="text" disabled name="gluu_oxd_port" min="0" max="65535"
                                                           value="<?php echo $gluu_config['gluu_oxd_port']; ?>"
                                                           style="width:400px; background-color: rgb(235, 235, 228);" placeholder="Please enter free port (for example 8099). (Min. number 0, Max. number 65535)."/>
                                                </td>
                                            </tr>
                                            <tr class="host">
                                                <td class="host"><b><font color="#FF0000">*</font>oxd https extension host:</b></td>
                                                <td class="host">
                                                    <input type="text" disabled="" style="width:400px; background-color: rgb(235, 235, 228);" value="<?php echo isset($gluu_config['gluu_oxd_host'])?$gluu_config['gluu_oxd_host']: ''; ?>" name="gluu_oxd_web_host" style="background-color: rgb(235, 235, 228);" placeholder="Please enter oxd https extension host">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style=" width: 40%"><b>oxd ID:</b></td>
                                                <td>
                                                    <input class="" type="text" disabled name="oxd_id"
                                                           value="<?php echo gluu_is_oxd_registered(); ?>"
                                                           style="width:400px;     background-color: rgb(235, 235, 228);" placeholder="Your oxd ID" />
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div style="padding-left: 20px;">
                                        <h3 style="padding-left: 10px;padding-bottom: 20px; border-bottom: 2px solid black; width: 60% ">Enrollment and Access Management
                                            <a data-toggle="tooltip" class="tooltipLink" data-original-title="Choose whether to register new users when they login at an external identity provider. If you disable automatic registration, new users will need to be manually created">
                                                <span class="glyphicon glyphicon-info-sign"></span>
                                            </a>
                                        </h3>
                                        <div>
                                            <p><label><input name="gluu_users_can_register" disabled type="radio" id="gluu_users_can_register" <?php if($gluu_users_can_register==1){ echo "checked";} ?> value="1" style="margin-right: 3px"><b> Automatically register any user with an account in the OpenID Provider</b></label></p>
                                        </div>
                                        <div>
                                            <p><label ><input name="gluu_users_can_register" disabled type="radio" id="gluu_users_can_register" <?php if($gluu_users_can_register==2){ echo "checked";} ?> value="2" style="margin-right: 3px"><b> Only register and allow ongoing access to users with one or more of the following roles in the OpenID Provider</b></label></p>
                                            <div style="margin-left: 20px;">
                                                <div id="p_role_disabled">
                                                    <?php $k=0;
                                                    if(!empty($gluu_new_roles)) {
                                                        foreach ($gluu_new_roles as $gluu_new_role) {
                                                            if (!$k) {
                                                                $k++;
                                                                ?>
                                                                <p class="role_p" style="padding-top: 10px">
                                                                    <input  type="text" name="gluu_new_role[]" disabled  style="display: inline"
                                                                            placeholder="Input role name" class="form-control"
                                                                            value="<?php echo $gluu_new_role; ?>"/>
                                                                    <button type="button" class="btn btn-xs " disabled="true"><span class="glyphicon glyphicon-plus"></span></button>
                                                                </p>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <p class="role_p" style="padding-top: 10px">
                                                                    <input type="text" name="gluu_new_role[]" disabled class="form-control"
                                                                           placeholder="Input role name" style="display: inline"
                                                                           value="<?php echo $gluu_new_role; ?>"/>
                                                                    <button type="button" class="btn btn-xs " disabled="true" ><span class="glyphicon glyphicon-plus"></span></button>
                                                                    <button type="button" class="btn btn-xs " disabled="true"><span class="glyphicon glyphicon-minus"></span></button>
                                                                </p>
                                                            <?php }
                                                        }
                                                    }else{
                                                        ?>
                                                        <p class="role_p" style="padding-top: 10px">
                                                            <input type="text" name="gluu_new_role[]" disabled placeholder="Input role name" class="form-control" style="display: inline" value=""/>
                                                            <button type="button" class="btn btn-xs " disabled="true" ><span class="glyphicon glyphicon-plus"></span></button>
                                                        </p>
                                                        <?php
                                                    }?>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <p><label><input name="gluu_users_can_register" disabled type="radio" id="gluu_users_can_register_2" <?php if($gluu_users_can_register==3){ echo "checked";} ?> value="3" style="margin-right: 3px"><b>Disable automatic registration</b></label></p>
                                        </div>
                                        <table>
                                            <tr>
                                                <td style="width: 54%;"><label for="UserType"><b>New User Default Role:</b></label></td>
                                                <td>
                                                    <?php
                                                    $user_types = array(
                                                        array('name'=>'Regular User', 'status'=>'0'),
                                                        array('name'=>'System Administrator User', 'status'=>'1')
                                                    );
                                                    ?>
                                                    <div class="form-group" style="margin-bottom: 0px !important;">
                                                        <select id="UserType" class="form-control" name="gluu_user_role" disabled>
                                                            <?php
                                                            foreach($user_types as $user_type){
                                                                ?>
                                                                <option <?php if($user_type['status'] == $gluu_user_role) echo 'selected'; ?> value="<?php echo $user_type['status'];?>"><?php echo $user_type['name'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <div style="border-bottom:2px solid #000;"></div>
                                        <br/><br/>
                                        <div class="row">
                                            <div class="col-md-3 col-md-offset-3 text-right">
                                                <a class="btn btn-primary" style="width: 120px;color:white!important;text-decoration:none !important;" href="index.php?module=Gluussos&action=generalEdit">Edit</a>
                                            </div>
                                            <div class="col-md-3 text-left">
                                                <input type="submit" onclick="return confirm('Are you sure that you want to remove this OpenID Connect provider? Users will no longer be able to authenticate against this OP.')" name="resetButton" value="Delete" style="width: 120px;height: 35px;background-color: #337ab7 !important; color:white;    background-image: none;" class="btn btn-primary btn-large"/>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>

                    <?php }?>
                </div>
            </div>
        </div>
</div>

