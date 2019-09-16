
<style>
    .sansTable {
        max-width: 100% !important;
        overflow-x:auto;
        border-collapse: collapse;
        border-style: hidden;   
    }
    .sansTable th, .sansTable td {        
    border: 1px solid #ddd;
    text-align: left;
    padding-left:8px;
    padding-right:2px;
    
    }
    #sansTd {
        padding: 0px !important;
    }
    .table {
        margin-bottom: 0px !important;
    }
    .modal .btn {
        margin: 2px !important;
    }
    #Action_Custom_Module_Button_Reissue_Certificate {
        margin: 2px !important;
    }
    #viewPrivateKey h4 {
        text-align: left !important;
    }
</style>
<script type="text/javascript" src="{$assetsURL}/js/mgLibs.js"></script>    
{if $allOk === true}
    <table id="mainTable" class="table table-bordered">
        <colgroup>
            <col style="width: 20%"/>
            <col style="width: 80%"/>
        </colgroup>
        <tbody>
            <tr>
                <td class="text-left" >{$MGLANG->T('configurationStatus')}</td>
                <td class="text-left">{$MGLANG->T($configurationStatus)}{if $configurationStatus === 'Awaiting Configuration'} - <a href="{$configurationURL}">{$MGLANG->T('configureNow')}</a>{/if}</td>
            </tr>
            {if $activationStatus}
                <tr>
                    <td class="text-left">{$MGLANG->T('activationStatus')}</td>
                    <td class="text-left">
                        {if $activationStatus === 'active'}
                            {$MGLANG->T('activationStatusActive')}
                        {elseif $activationStatus === 'new_order'}
                            {$MGLANG->T('activationStatusNewOrder')}
                        {elseif $activationStatus === 'pending'}
                            {$MGLANG->T('activationStatusPending')}
                        {elseif $activationStatus === 'cancelled'}
                            {$MGLANG->T('activationStatusCancelled')}
                        {elseif $activationStatus === 'payment needed'}
                            {$MGLANG->T('activationStatusPaymentNeeded')}
                        {elseif $activationStatus === 'processing'}
                            {$MGLANG->T('activationStatusProcessing')}
                        {elseif $activationStatus === 'incomplete'}
                            {$MGLANG->T('activationStatusIncomplete')}
                        {elseif $activationStatus === 'rejected'}
                            {$MGLANG->T('activationStatusRejected')}
                        {else}
                            {$activationStatus|ucfirst}
                        {/if}
                    </td>
                </tr>
            {/if}
            {if $activationStatus === 'active'}            
                <tr>
                    <td class="text-left">{$MGLANG->T('validFrom')}</td>
                    <td class="text-left">{$validFrom}</td>
                </tr>
                <tr>
                    <td class="text-left">{$MGLANG->T('validTill')}</td>
                    <td class="text-left">{$validTill}</td>
                </tr>
            {/if}
            <!--{if $order_id}
                <tr>
                    <td class="text-left">{$MGLANG->T('Order ID')}</td>
                    <td class="text-left">{$order_id}</td>
                </tr>
            {/if}
            -->
            {if $digitalsign_id}
                <tr>
                    <td class="text-left">{$MGLANG->T('Partner Order ID')}</td>
                    <td class="text-left">{$digitalsign_id}</td>
                </tr>
            {/if}
            
            {if $sans}
                <tr>
                    <td class="text-left">{$MGLANG->T('domain')}</td>
                    <td id="sansTd" colspan="2" class="text-left">
                            <table class="sansTable table table-bordered" >
                            <tbody>
                            {foreach from=$sans item=san} 
                                <tr>
                                    <td colspan="2" class="text-center">
                                        <b>{$MGLANG->T({$san.san})}</b>
                                        ({$san.type})

                                        <select class="form-control select-dcv" data-domain="{$san.san}" style="display: inline-block; width: 80px;">
                                            <option value="http"
                                                {if $san['type'] == 'http'}
                                                    selected
                                                {/if}
                                            >http</option>
                                            <option value="https"
                                                {if $san['type'] == 'https'}
                                                    selected
                                                {/if}
                                            >https</option>
                                            <option value="dns"
                                                {if $san['type'] == 'dns'}
                                                    selected
                                                {/if}
                                            >dns</option>
                                            <option value="email"
                                                {if $san['type'] == 'email'}
                                                    selected
                                                {/if}
                                            >email</option>
                                        </select>
                                        <select class="form-control select-email" data-domain="{$san.san}" style="
                                            {if $san['type'] == 'email'}
                                            display: inline-block;
                                            {else}
                                            display: none;
                                            {/if}
                                        width: 200px;">
                                            <option value="admin@{$MGLANG->T({$san.san})}">admin@{$MGLANG->T({$san.san})}</option>
                                            <option value="administrator@{$MGLANG->T({$san.san})}">administrator@{$MGLANG->T({$san.san})}</option>
                                            <option value="postmaster@{$MGLANG->T({$san.san})}">postmaster@{$MGLANG->T({$san.san})}</option>
                                            <option value="hostmaster@{$MGLANG->T({$san.san})}">hostmaster@{$MGLANG->T({$san.san})}</option>
                                            <option value="webmaster@{$MGLANG->T({$san.san})}">webmaster@{$MGLANG->T({$san.san})}</option>
                                            {if preg_replace('/^www\./', '', $san['san']) != $san['san']}
                                                <option value="admin@{preg_replace('/^www\./', '', $san['san'])}">admin@{preg_replace('/^www\./', '', $san['san'])}</option>
                                                <option value="administrator@{preg_replace('/^www\./', '', $san['san'])}">administrator@{preg_replace('/^www\./', '', $san['san'])}</option>
                                                <option value="postmaster@{preg_replace('/^www\./', '', $san['san'])}">postmaster@{preg_replace('/^www\./', '', $san['san'])}</option>
                                                <option value="hostmaster@{preg_replace('/^www\./', '', $san['san'])}">hostmaster@{preg_replace('/^www\./', '', $san['san'])}</option>
                                                <option value="webmaster@{preg_replace('/^www\./', '', $san['san'])}">webmaster@{preg_replace('/^www\./', '', $san['san'])}</option>
                                            {/if}
                                        </select>
                                        <a href="javascript:void(0);" class="btn btn-default btn-change-dcv" data-domain="{$san.san}">修改验证</a>
                                    </td>
                                </tr>
                                {if $activationStatus == 'pending'}
                                {if $san.type == 'http'}
                                    <tr>
                                        <td style="width: 15%" class="text-left">{$MGLANG->T('hashFile')}</td>
                                        <td class="text-left" style="max-width:200px; word-wrap: break-word;">{$san.http.url}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%" class="text-left">{$MGLANG->T('content')}</td>
                                        <td class="text-left" style="max-width:200px; word-wrap: break-word;">{str_replace(PHP_EOL, '<br/>', $san.http.filecontent)}</td>
                                    </tr> 
                                {else}
                                {if $san.type == 'https'}
                                    <tr>
                                        <td style="width: 15%" class="text-left">{$MGLANG->T('hashFile')}</td>
                                        <td class="text-left" style="max-width:200px; word-wrap: break-word;">{$san.https.url}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%" class="text-left">{$MGLANG->T('content')}</td>
                                        <td class="text-left" style="max-width:200px; word-wrap: break-word;">{str_replace(PHP_EOL, '<br/>', $san.https.filecontent)}</td>
                                    </tr> 
                                {else}
                                        {if $san.type == 'dns'}
                                            <tr>
                                                <td style="width: 15%" class="text-left">{$MGLANG->T('dnsCnameRecord')}</td>
                                                <td class="text-left" style="max-width:200px; word-wrap: break-word;">{$san.dns.type}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 15%" class="text-left">主机名</td>
                                                <td class="text-left" style="max-width:200px; word-wrap: break-word;">{$san.dns.fullname}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 15%" class="text-left">记录值</td>
                                                <td class="text-left" style="max-width:200px; word-wrap: break-word;">{$san.dns.value}</td>
                                            </tr> 
                                        {else}
                                            {if $san.type == 'email'}
                                            <tr>
                                                <td style="width: 15%" class="text-left">{$MGLANG->T('validationEmail')}</td>
                                                <td class="text-left" style="word-wrap: break-word;">请进入 <span class="span-email" data-domain="{$san.san}">{$san.email.address}</span> 收信</td>
                                            </tr> 
                                            {/if}
                                        {/if} 
                                {/if}
                                {/if}
                                {/if}
                            {/foreach}
                            </tbody>
                        </table>                        
                    </td>
                </tr>
               <!--<tr>
                    <td class="text-left">{$MGLANG->T('sans')}</td>
                    <td class="text-left">{$sans}</td>
                </tr>-->
            {/if}
            {if $crt}
                <tr>
                    <td class="text-left">{$MGLANG->T('crt')}</td>
                    <td class="text-left"><textarea onfocus="this.select()" rows="5" class="form-control">{$crt}</textarea></td>
                </tr>
            {/if}
            {if $ca}
                <tr>
                    <td class="text-left">{$MGLANG->T('ca_chain')}</td>
                    <td class="text-left"><textarea onfocus="this.select()" rows="5" class="form-control">{$ca}</textarea></td>
                </tr>
            {/if}            
            {if $csr}
                <tr>
                    <td class="text-left">{$MGLANG->T('csr')}</td>
                    <td class="text-left"><textarea onfocus="this.select()" rows="5" class="form-control">{$csr}</textarea></td>
                </tr>
            {/if}
            <tr id="additionalActionsTr">
                <td class="text-left">{$MGLANG->T('Actions')}</td>
                <td id="additionalActionsTd" class="text-left">
                    {if $displayRenewButton}
                        <button type="button" id="btnRenew" class="btn btn-default" style="margin:2px">{$MGLANG->T('renew')}</button>
                    {/if}
                    {if $activationStatus == 'pending'}
                        <button type="button" id="resend-validation-email" class="btn btn-default" style="margin:2px">{$MGLANG->T('resendValidationEmail')} / {$MGLANG->T('revalidate')}</button>
                    {/if}
                    {if $configurationStatus != 'Awaiting Configuration'}
                        {if $dcv_method == 'email' && !$sans}
                            <button type="button" id="btnChange_Approver_Email" class="btn btn-default" style="margin:2px">{$MGLANG->T('changeValidationEmail')}</button>
                        {/if}
                        {if $activationStatus == 'active'}
                            <a class="btn btn-default" role="button" href="" id="Action_Custom_Module_Button_Reissue_Certificate">{$MGLANG->T('reissueCertificate')}</a>
                            <button type="button" id="send-certificate-email" class="btn btn-default" style="margin:2px">{$MGLANG->T('sendCertificate')}</button>
                        {/if}                        
                        <!--<button type="button" id="{if $dcv_method == 'email'}btnChange_Approver_Email{else}btnRevalidate{/if}" class="btn btn-default" style="margin:2px">{if $dcv_method == 'email'}{$MGLANG->T('changeValidationEmail')}{else}{$MGLANG->T('revalidate')}{/if}</button>-->                     
                    {/if}  
                </td>
            </tr>
        </tbody>
    </table>
    <script type="text/javascript">
        $(document).ready(function () {
            {if $activationStatus !== 'active'} 
                //$('#Primary_Sidebar-Service_Details_Actions-Custom_Module_Button_Reissue_Certificate').remove();
            {else}
                $('#resend-validation-email').remove();
                $('#btnChange_Approver_Email').remove();
            {/if}
            var reissueUrl= $('#Primary_Sidebar-Service_Details_Actions-Custom_Module_Button_Reissue_Certificate').attr('href');
            $('#Action_Custom_Module_Button_Reissue_Certificate').prop('href', reissueUrl);
            $('#Primary_Sidebar-Service_Details_Actions-Custom_Module_Button_Reissue_Certificate').remove();            
        });
    </script>
    
    <!--RENEW MODAL-->
    <div class="modal fade" id="modalRenew" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content panel panel-primary">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">{$MGLANG->T('Close')}</span>
                    </button>
                    <h4 class="modal-title">{$MGLANG->T('renewModalTitle')}</h4>
                </div>
                <div class="modal-body panel-body" id="modalRenewBody">
                    
                    <div class="alert alert-success hidden" id="modalRenewSuccess">
                        <strong>Success!</strong> <span></span>
                    </div>
                    <div class="alert alert-danger hidden" id="modalRenewDanger">
                        <strong>Error!</strong> <span></span>
                    </div>
                    <form class="form-horizontal" role="form" id="modalRenewForm">
                            <div class="col-sm-12" style="padding: 25px;">
                                {$MGLANG->T('renewModalConfirmInformation')}
                            </div> 
                    </form>
                </div>
                <div class="modal-footer panel-footer">
                    <button type="button" id="modalRenewSubmit" class="btn btn-primary">
                        {$MGLANG->T('Submit')}
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {$MGLANG->T('Close')}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            
            var serviceUrl = 'clientarea.php?action=productdetails&id={$serviceid}&json=1',
                    renewBtn = $('#btnRenew'),
                    renewForm,
                    renewModal,
                    renewBody,
                    renewInput,
                    renewDangerAlert,
                    renewSuccessAlert,
                    renewSubmitBtn,
                    body = $('body');
            
            function assignModalElements(init) {
                renewModal = $('#modalRenew');
                renewBody = $('#modalRenewBody');

                if (init) {
                    renewBody.contents()
                    .filter(function(){
                        return this.nodeType === 8;
                    })
                    .replaceWith(function(){
                        return this.data;
                    });
                }

                if (!init) {
                    renewForm = $('#modalRenewForm');
                    renewSubmitBtn = $('#modalRenewSubmit');
                    //renewInput = $('.modalRenewInput');
                    renewBody = $('#modalRenewBody');
                    renewDangerAlert = $('#modalRenewDanger');
                    renewSuccessAlert = $('#modalRenewSuccess');
                }
            }

            function moveModalToBody() {
                
                body.append(renewModal.clone());
                assignModalElements(false);                
                renewModal.remove();
            }

            function unbindOnClickForrenewBtn() {
                renewBtn.attr('onclick', '');
            }

            function bindModalFrorenewBtn() {
                renewBtn.off().on('click', function () {
                    renewModal.modal('show');
                    show(renewSubmitBtn);
                    show(renewForm);
                    hideAll();
                });
            }

            function bindSubmitBtn() {
                renewSubmitBtn.off().on('click', function () {
                    submitrenewModal();
                });
            }

            function showSuccessAlert(msg) {
                var reloadInfo = '{$MGLANG->T('redirectToInvoiceInformation')}'
                show(renewSuccessAlert);
                hide(renewDangerAlert);                
                renewSuccessAlert.children('span').html(msg + ' ' + reloadInfo);
            }

            function showDangerAlert(msg) {
                hide(renewSuccessAlert);
                show(renewDangerAlert);
                renewDangerAlert.children('span').html(msg);
            }

            function addSpiner(element) {
                element.append('<i class="fa fa-spinner fa-spin"></i>');
            }

            function removeSpiner(element) {
                element.find('.fa-spinner').remove();
            }

            function show(element) {
                element.removeClass('hidden');
            }

            function hide(element) {
                element.addClass('hidden');
            }

            function enable(element) {
                element.removeAttr('disabled')
                element.removeClass('disabled');
            }

            function disable(element) {
                element.attr("disabled", true);
                element.addClass('disabled');
            }

            function hideAll() {
                hide(renewDangerAlert);
                hide(renewSuccessAlert);
            }

            function anErrorOccurred() {
                showDangerAlert('{$MGLANG->T('anErrorOccurred')}');
            }

            function isJsonString(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }
            
            function resize(element) {
                element.css('height', "");
            }

            function submitrenewModal() {
                addSpiner(renewSubmitBtn);
                disable(renewSubmitBtn);
                
                var data = {
                    renewModal: 'yes',
                    serviceId: {$serviceid},
                    userID: {$userid},
                    'mg-action': 'renew'
                };
                $.ajax({
                    url: serviceUrl,
                    data: data,
                    json: 1,
                    success: function (ret) {
                        var data;
                        ret = ret.replace("<JSONRESPONSE#", "");
                        ret = ret.replace("#ENDJSONRESPONSE>", "");
                        if (!isJsonString(ret)) {
                            anErrorOccurred();
                            return;
                        }
                        data = JSON.parse(ret);
                        if (data.success === 1 || data.success === true) {
                            showSuccessAlert(data.data.msg);
                            hide(renewSubmitBtn);
                            resize(renewBody);
                            hide(renewForm);   
                            window.setTimeout(function(){ window.location.replace('viewinvoice.php?id=' + data.data.invoiceID) }, 5000);
                        } else {    
                            if(typeof data.data.invoiceID !== 'undefined')
                            {   
                                var reloadInfo = '{$MGLANG->T('redirectToInvoiceInformation')}'
                                showDangerAlert(data.error + ' ' + reloadInfo);
                                window.setTimeout(function(){ window.location.replace('viewinvoice.php?id=' + data.data.invoiceID) }, 5000);
                            } else {
                                showDangerAlert(data.error);
                            } 
                        }
                    },
                    error: function (jqXHR, errorText, errorThrown) {
                        anErrorOccurred();
                    },
                    complete: function () {
                        removeSpiner(renewSubmitBtn);
                        enable(renewSubmitBtn);
                    }
                });
            }
            
            assignModalElements(true);
            moveModalToBody();
            renewForm.trigger("reset");
            unbindOnClickForrenewBtn();
            bindModalFrorenewBtn();
            bindSubmitBtn(); 
        });
    </script>
    <!--END RENEW MODAL-->
    <div class="modal fade" id="modalRevalidate" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content panel panel-primary">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">{$MGLANG->T('Close')}</span>
                    </button>
                    <h4 class="modal-title">{$MGLANG->T('revalidateModalTitle')}</h4>
                </div>
                <div {if $sans && !$brand|in_array:$brandsWithOnlyEmailValidation}style="overflow-y: auto; height:{if $sans|@count == 1 }200{elseif $sans|@count == 2}275{else}350{/if}px;"{/if} class="modal-body panel-body" id="modalRevalidateBody">
                    
                    <div class="alert alert-success hidden" id="modalRevalidateSuccess">
                        <strong>Success!</strong> <span></span>
                    </div>
                    <div class="alert alert-danger hidden" id="modalRevalidateDanger">
                        <strong>Error!</strong> <span></span>
                    </div>
                    <form class="form-horizontal" role="form" id="modalRevalidateForm">
                            <div class="col-sm-12">
                                <table class="table revalidateTable">
                                    <thead>
                                        <tr>
                                            <th>{$MGLANG->T('revalidateModalDomainLabel')}</th>
                                            <th style="width:35%;">{$MGLANG->T('revalidateModalMethodLabel')}</th>
                                            <th> {if 'email'|in_array:$disabledValidationMethods} {else}{$MGLANG->T('revalidateModalEmailLabel')}{/if}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{$domain}</td>
                                            <td>
                                                <div class="form-group">  
                                                    <select style="width:70%;" type="text" name="newDcvMethod_0" class="form-control modalRevalidateInput" >
                                                        <option value="" selected>{$MGLANG->T('pleaseChooseOne')}</option>
                                                        {if !'email'|in_array:$disabledValidationMethods}
                                                            <option value="email">{$MGLANG->T('revalidateModalMethodEmail')}</option>
                                                        {/if}                                                        
                                                        {if !$brand|in_array:$brandsWithOnlyEmailValidation}                                                            
                                                        <option value="http">{$MGLANG->T('revalidateModalMethodHttp')}</option>
                                                        <option value="https">{$MGLANG->T('revalidateModalMethodHttps')}</option>
                                                        <option value="dns">{$MGLANG->T('revalidateModalMethodDns')}</option>                                                        
                                                        {/if}
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display:none;" class="form-group newApproverEmailFormGroup_0">
                                                    <select type="text" name="newApproverEmailInput_0"class="form-control newApproverEmailInputValidation"/>
                                                        <option id="loadingDomainEmails">{$MGLANG->T('loading')}</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        {if $sans && !$brand|in_array:$brandsWithOnlyEmailValidation}
                                            {$i = 1}
                                            {foreach from=$sans item=san}
                                                <tr>
                                                    <td>{$san.san_name}</td>
                                                    <td>
                                                        <div class="form-group">  
                                                            <select style="width:70%;" type="text" name="newDcvMethod_{$i}" class="form-control modalRevalidateInput">
                                                                <option value="" selected>{$MGLANG->T('pleaseChooseOne')}</option>
                                                                {if !'email'|in_array:$disabledValidationMethods}
                                                                    <option value="email">{$MGLANG->T('revalidateModalMethodEmail')}</option>
                                                                {/if} 
                                                                {if !$brand|in_array:$brandsWithOnlyEmailValidation}                                                            
                                                                <option value="http">{$MGLANG->T('revalidateModalMethodHttp')}</option>
                                                                <option value="https">{$MGLANG->T('revalidateModalMethodHttps')}</option>
                                                                <option value="dns">{$MGLANG->T('revalidateModalMethodDns')}</option>                                                        
                                                                {/if}
                                                            </select>
                                                        </div>
                                                    <td>
                                                        <div style="display:none;" class="form-group newApproverEmailFormGroup_{$i}">
                                                            <select type="text" name="newApproverEmailInput_{$i}" class="form-control newApproverEmailInputValidation"/>
                                                                <option id="loadingDomainEmails">{$MGLANG->T('loading')}</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>  
                                            {$i=$i+1}
                                            {/foreach}
                                        {/if}
                                    </tbody>
                                </table>
                            </div> 
                    </form>
                </div>
                <div class="modal-footer panel-footer">
                    <button type="button" id="modalRevalidateSubmit" class="btn btn-primary">
                        {$MGLANG->T('Submit')}
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {$MGLANG->T('Close')}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            
            var serviceUrl = 'clientarea.php?action=productdetails&id={$serviceid}&json=1',
                    revalidateBtn = $('#btnRevalidate'),
                    revalidateForm,
                    revalidateModal,
                    revalidateBody,
                    revalidateInput,
                    revalidateDangerAlert,
                    revalidateSuccessAlert,
                    revalidateSubmitBtn,
                    body = $('body');
            
            function assignModalElements(init) {
                revalidateModal = $('#modalRevalidate');
                revalidateBody = $('#modalRevalidateBody');

                if (init) {
                    revalidateBody.contents()
                    .filter(function(){
                        return this.nodeType === 8;
                    })
                    .replaceWith(function(){
                        return this.data;
                    });
                }

                if (!init) {
                    revalidateForm = $('#modalRevalidateForm');
                    revalidateSubmitBtn = $('#modalRevalidateSubmit');
                    revalidateInput = $('.modalRevalidateInput');
                    revalidateBody = $('#modalRevalidateBody');
                    revalidateEmail = $('.newApproverEmailInputValidation');
                    revalidateDangerAlert = $('#modalRevalidateDanger');
                    revalidateSuccessAlert = $('#modalRevalidateSuccess');
                }
            }

            function moveModalToBody() {
                
                body.append(revalidateModal.clone());
                assignModalElements(false);                
                revalidateModal.remove();
            }

            function unbindOnClickForrevalidateBtn() {
                revalidateBtn.attr('onclick', '');
            }

            function bindModalFrorevalidateBtn() {
                revalidateBtn.off().on('click', function () {
                    revalidateModal.modal('show');
                    show(revalidateSubmitBtn);
                    show(revalidateForm);
                    hideAll();
                });
            }

            function bindSubmitBtn() {
                revalidateSubmitBtn.off().on('click', function () {
                    submitrevalidateModal();
                });
            }

            function showSuccessAlert(msg) {
                var reloadInfo = '{$MGLANG->T('reloadInformation')}'
                show(revalidateSuccessAlert);
                hide(revalidateDangerAlert);                
                revalidateSuccessAlert.children('span').html(msg + ' ' + reloadInfo);
            }

            function showDangerAlert(msg) {
                hide(revalidateSuccessAlert);
                show(revalidateDangerAlert);
                revalidateDangerAlert.children('span').html(msg);
            }

            function addSpiner(element) {
                element.append('<i class="fa fa-spinner fa-spin"></i>');
            }

            function removeSpiner(element) {
                element.find('.fa-spinner').remove();
            }

            function show(element) {
                element.removeClass('hidden');
            }

            function hide(element) {
                element.addClass('hidden');
            }

            function enable(element) {
                element.removeAttr('disabled')
                element.removeClass('disabled');
            }

            function disable(element) {
                element.attr("disabled", true);
                element.addClass('disabled');
            }

            function hideAll() {
                hide(revalidateDangerAlert);
                hide(revalidateSuccessAlert);
            }

            function anErrorOccurred() {
                showDangerAlert('{$MGLANG->T('anErrorOccurred')}');
            }

            function isJsonString(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }
            
            function resize(element) {
                element.css('height', "");
            }

            function submitrevalidateModal() {
                addSpiner(revalidateSubmitBtn);
                disable(revalidateSubmitBtn);
                var newMethods = {};
                revalidateInput.each(function(key,value){                    
                    var node = $('.revalidateTable>tbody').find('tr:eq('+key+')').find('td:eq(0)')[1];
                    if(typeof node !== 'undefined') {
                        domain = node.textContent;
                    }
                    domain = domain.replace("*", "___"); 
                    if(this.value === 'email') {
                        if(key === 0) {                            
                            newMethods[domain] = $('select[name="newApproverEmailInput_'+key+'"]')[2].value;
                        } else {
                            newMethods[domain] = $('select[name="newApproverEmailInput_'+key+'"]')[1].value;
                        }
                    } else {
                        if(this.value !== "") {
                            newMethods[domain] = this.value;
                        }                        
                    }
                });
                if(jQuery.isEmptyObject(newMethods)) {
                    showDangerAlert('{$MGLANG->T('noValidationMethodSelected')}');
                    removeSpiner(revalidateSubmitBtn);
                    enable(revalidateSubmitBtn);
                    return;
                }
                var noEmailError = '';
                $.each(newMethods,function(key, value){
                    if(value === '{$MGLANG->T('pleaseChooseOne')}' || value === '{$MGLANG->T('loading')}') {                       
                        noEmailError = '{$MGLANG->T('noEmailSelectedForDomain')}' + key.replace("___", "*");
                        return true;                        
                    }
                });
                if(noEmailError !== '') {
                    showDangerAlert(noEmailError);
                    removeSpiner(revalidateSubmitBtn);
                    enable(revalidateSubmitBtn);
                    return;    
                }
                var data = {
                    revalidateModal: 'yes',
                    newDcvMethods: newMethods,
                    serviceId: {$serviceid},
                    userID: {$userid},
                    'mg-action': 'revalidate'
                };
                $.ajax({
                    url: serviceUrl,
                    data: data,
                    json: 1,
                    success: function (ret) {
                        var data;
                        ret = ret.replace("<JSONRESPONSE#", "");
                        ret = ret.replace("#ENDJSONRESPONSE>", "");
                        if (!isJsonString(ret)) {
                            anErrorOccurred();
                            return;
                        }
                        data = JSON.parse(ret);
                        if (data.success === 1 || data.success === true) {
                            showSuccessAlert(data.data.msg);                            
                            revalidateInput.val('');
                            hide(revalidateSubmitBtn);
                            resize(revalidateBody);
                            hide(revalidateForm);   
                            window.setTimeout(function(){ location.reload() }, 5000);
                        } else {
                            showDangerAlert(data.data.msg);
                        }
                    },
                    error: function (jqXHR, errorText, errorThrown) {
                        anErrorOccurred();
                    },
                    complete: function () {
                        removeSpiner(revalidateSubmitBtn);
                        enable(revalidateSubmitBtn);
                    }
                });
            }
            
            assignModalElements(true);
            moveModalToBody();
            revalidateForm.trigger("reset");
            unbindOnClickForrevalidateBtn();
            bindModalFrorevalidateBtn();
            bindSubmitBtn();            
            revalidateInput.on("change", function() {
                    var fieldIndex = this.name.replace('newDcvMethod_', '');
                    var domain = $(this).closest('td').prev('td').text();
                    var selectedMethod = '';
                    selectedMethod = $(this).find(":selected").val();
                    if(selectedMethod === 'email') {
                        $(".newApproverEmailFormGroup_"+fieldIndex).css('display', 'block');
                        getDomainEmails(null, domain, fieldIndex);
                    } else {
                        $(".newApproverEmailFormGroup_"+fieldIndex).css('display', 'none');
                    }                    
            });
        });
    </script>
    <div class="modal fade" id="modalChangeApprovedEmail" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content panel panel-primary">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title">{$MGLANG->T('changeApproverEmailModalModalTitle')}</h4>
                </div>
                <div class="modal-body panel-body" id="modalChangeApprovedEmailBody">
                    <div class="alert alert-success hidden" id="modalChangeApprovedEmailSuccess">
                        <strong>Success!</strong> <span></span>
                    </div>
                    <div class="alert alert-danger hidden" id="modalChangeApprovedEmailDanger">
                        <strong>Error!</strong> <span></span>
                    </div>
                    <div class="form-group newApproverEmailFormGroup">
                        <label class="col-sm-3 control-label">{$MGLANG->T('newApproverEmailModalModalLabel')}</label>
                        <div class="col-sm-9">
                            <select type="text" name="newApproverEmailInput_0" id="modalChangeApprovedEmailInput" class="form-control"/>
                                <option id="loadingDomainEmails">{$MGLANG->T('loading')}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer panel-footer">
                    <button type="button" id="modalChangeApprovedEmailSubmit" class="btn btn-primary">
                        {$MGLANG->T('Submit')}
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {$MGLANG->T('Close')}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            var serviceUrl = 'clientarea.php?action=productdetails&id={$serviceid}',
                    changeEmailBtn = $('#btnChange_Approver_Email'),
                    changeEmailForm,
                    changeEmailModal,
                    changeEmailBody,
                    changeEmailInput,
                    changeEmailDangerAlert,
                    changeEmailSuccessAlert,
                    changeEmailSubmitBtn,
                    body = $('body');
            function assignModalElements(init) {
                changeEmailModal = $('#modalChangeApprovedEmail');
                changeEmailBody = $('#modalChangeApprovedEmailBody');

                if (init) {
                    changeEmailBody.contents()
                    .filter(function(){
                        return this.nodeType === 8;
                    })
                    .replaceWith(function(){
                        return this.data;
                    });
                }

                if (!init) {
                    changeEmailForm = $('.newApproverEmailFormGroup');
                    changeEmailSubmitBtn = $('#modalChangeApprovedEmailSubmit');
                    changeEmailInput = $('#modalChangeApprovedEmailInput');
                    changeEmailDangerAlert = $('#modalChangeApprovedEmailDanger');
                    changeEmailSuccessAlert = $('#modalChangeApprovedEmailSuccess');
                }
            }

            function moveModalToBody() {
                body.append(changeEmailModal.clone());
                assignModalElements(false);
                
                changeEmailModal.remove();
            }

            function unbindOnClickForChangeEmailBtn() {
                changeEmailBtn.attr('onclick', '');
            }

            function bindModalFroChangeEmailBtn() {
                changeEmailBtn.off().on('click', function () {
                    changeEmailModal.modal('show');
                    show(changeEmailSubmitBtn);
                    show(changeEmailForm);
                    hideAll();
                });
            }

            function bindSubmitBtn() {
                changeEmailSubmitBtn.off().on('click', function () {
                    submitChangeEmailModal();
                });
            }

            function showSuccessAlert(msg) {
                var reloadInfo = '{$MGLANG->T('reloadInformation')}'
                show(changeEmailSuccessAlert);
                hide(changeEmailDangerAlert);
                changeEmailSuccessAlert.children('span').html(msg + ' ' + reloadInfo);
            }

            function showDangerAlert(msg) {
                hide(changeEmailSuccessAlert);
                show(changeEmailDangerAlert);
                changeEmailDangerAlert.children('span').html(msg);
            }

            function addSpiner(element) {
                element.append('<i class="fa fa-spinner fa-spin"></i>');
            }

            function removeSpiner(element) {
                element.find('.fa-spinner').remove();
            }

            function show(element) {
                element.removeClass('hidden');
            }

            function hide(element) {
                element.addClass('hidden');
            }

            function enable(element) {
                element.removeAttr('disabled')
                element.removeClass('disabled');
            }

            function disable(element) {
                element.attr("disabled", true);
                element.addClass('disabled');
            }

            function hideAll() {
                hide(changeEmailDangerAlert);
                hide(changeEmailSuccessAlert);
            }

            function anErrorOccurred() {
                showDangerAlert('{$MGLANG->T('anErrorOccurred')}');
            }

            function isJsonString(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }            

            function submitChangeEmailModal() {
                addSpiner(changeEmailSubmitBtn);
                disable(changeEmailSubmitBtn);

                var data = {
                    newEmail: changeEmailInput.val(),
                    serviceId: {$serviceid},
                    userID: {$userid},
                    json: 1,
                    'mg-action': 'changeApproverEmail'
                };
                $.ajax({
                    type: "POST",
                    url: serviceUrl,
                    data: data,
                    success: function (ret) {
                        var data;
                        ret = ret.replace("<JSONRESPONSE#", "");
                        ret = ret.replace("#ENDJSONRESPONSE>", "");
                        if (!isJsonString(ret)) {
                            anErrorOccurred();
                            return;
                        }
                        data = JSON.parse(ret);
                        if (data.success) {
                            showSuccessAlert(data.data.msg);
                            changeEmailInput.val('');
                            hide(changeEmailSubmitBtn);
                            hide(changeEmailForm);
                            window.setTimeout(function(){ location.reload() }, 5000);
                        } else {
                            showDangerAlert(data.error);
                        }
                    },
                    error: function (jqXHR, errorText, errorThrown) {
                        anErrorOccurred();
                    },
                    complete: function () {
                        removeSpiner(changeEmailSubmitBtn);
                        enable(changeEmailSubmitBtn);
                    }
                });
            }

            assignModalElements(true);
            moveModalToBody();
            unbindOnClickForChangeEmailBtn();
            bindModalFroChangeEmailBtn();
            bindSubmitBtn();
        });
    </script>    
{/if}   
    <div class="modal fade" id="viewPrivateKey" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content panel panel-primary">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title">{$MGLANG->T('viewPrivateKeyModalTitle')}</h4>
                </div>
                <div class="modal-body panel-body" id="modalViewPrivateKey">                    
                     <div class="form-group">
                        <textarea id="privateKey" class="form-control"  rows="13" style="overflow:auto;resize:none"></textarea>
                     </div> 
                </div>
                <div class="modal-footer panel-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {$MGLANG->T('Close')}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        
        {literal}
            
            function getDomainEmails(serviceid = null, domain, index){
                var brand = {/literal}'{$brand}'{literal};  
                var serviceUrl = 'clientarea.php?action=productdetails&json=1&mg-action=getApprovalEmailsForDomain&brand=' + brand + '&domain=' + domain;
                                 
                serviceUrl += '&id=' + {/literal}'{$serviceid}'{literal};
                
                $.ajax({
                        type: "POST",
                        url: serviceUrl,
                        success: function (ret) {
                            var data;    
                            $('select[name="newApproverEmailInput_'+index+'"]').empty();
                            ret = ret.replace("<JSONRESPONSE#", "");
                            ret = ret.replace("#ENDJSONRESPONSE>", "");
                            
                            data = JSON.parse(ret);
                            if (data.success === 1) {
                                var  htmlOptions = [];
                                htmlOptions += '<option>'+{/literal}'{$MGLANG->T('pleaseChooseOne')}'{literal}+'</option>';
                                var domainEmails = data.data.domainEmails;
                                for (var i = 0; i < domainEmails.length; i++) {  
                                     htmlOptions += '<option value="' + domainEmails[i] + '">' + domainEmails[i] + '</option>';                                        
                                }
                                
                                $('select[name="newApproverEmailInput_'+index+'"]').append(htmlOptions);
                            } else {
                                showDangerAlert(data.msg);
                            }
                        },
                        error: function (jqXHR, errorText, errorThrown) {
                            nErrorOccurred();
                        }
                    });
            }
            $(document).ready(function () {
                
                var serviceid = {/literal}'{$serviceid}'{literal};  
                var domain =   {/literal}'{$domain}'{literal};  
                jQuery('#btnChange_Approver_Email').on("click", function(){
                    getDomainEmails(serviceid, domain, 0);
                });    
                var additionalActions = $('#additionalActionsTd').html().trim();
                if(additionalActions.length == 0) {
                    $('#additionalActionsTr').remove();
                }
                jQuery('#resend-validation-email').on("click",function(){
                    $('#resend-validation-email').append(' <i id="resendSpinner" class="fa fa-spinner fa-spin"></i>');
                    JSONParser.request('resendValidationEmail',{json: 1, id: serviceid}, function (data) {
                        if (data.success == true) {                            
                            $('#MGAlerts>div[data-prototype="success"]').show();
                            $('#MGAlerts>div[data-prototype="success"] strong').html(data.message);
                        } else if (data.success == false) {
                            $('#MGAlerts>div[data-prototype="error"]').show();
                            $('#MGAlerts>div[data-prototype="error"] strong').html(data.message);
                        }
                        $('#resend-validation-email').find('.fa-spinner').remove();
                    }, false);
                });
                jQuery('#send-certificate-email').on("click",function(){
                    $('#send-certificate-email').find('.fa-spinner').remove();
                    $('#send-certificate-email').append(' <i id="resendSpinner" class="fa fa-spinner fa-spin"></i>');
                    JSONParser.request('sendCertificateEmail',{json: 1, id: serviceid}, function (data) {
                        if (data.success == true) {                            
                            $('#MGAlerts>div[data-prototype="success"]').show();
                            $('#MGAlerts>div[data-prototype="success"] strong').html(data.message);
                        } else if (data.success == false) {
                            $('#MGAlerts>div[data-prototype="error"]').show();
                            $('#MGAlerts>div[data-prototype="error"] strong').html(data.message);
                        }
                        $('#send-certificate-email').find('.fa-spinner').remove();
                    }, false);
                });
                jQuery('#getPrivateKey').on("click",function(){
                    
                    $('#getPrivateKey').append(' <i class="fa fa-spinner fa-spin"></i>');
                    JSONParser.request('getPrivateKey',{json: 1,id: serviceid}, function (data) {
                        if (data.success == true) {
                            $('#MGAlerts>div').css('display', 'none');
                            $('#getPrivateKey').find('.fa-spinner').remove();
                            $('#viewPrivateKey').modal('toggle');
                            $('#privateKey').text(data.privateKey);
                        } else if (data.success == false) {
                            $('#getPrivateKey').find('.fa-spinner').remove();
                            $('#MGAlerts>div[data-prototype="error"]').show();
                            $('#MGAlerts>div[data-prototype="error"] strong').html(data.message);
                        }
                    }, false);
                });
                
                jQuery('#reissue-order').on("click",function(){
                    JSONParser.request('reIssueOrder',{json: 1}, function (data) {
                        if (data.success == true) {
                            $('#MGAlerts>div[data-prototype="success"]').show();
                            $('#MGAlerts>div[data-prototype="success"] strong').html(data.message);
                        } else if (data.success == false) {
                            $('#MGAlerts>div[data-prototype="error"]').show();
                            $('#MGAlerts>div[data-prototype="error"] strong').html(data.message);
                        }
                    }, false);
                });
                
                //for template simplicity modal header bug
                var color = $('#modalRevalidate').find('.panel-heading').css('background-color');
                $('#viewPrivateKey').find('.panel-heading').css('background-color', color);
            });


            (function ($) {
                $('.span-email').each(function () {
                    var spanEmail = $(this);
                    var domain = spanEmail.attr('data-domain');
                    var email = spanEmail.text();
                    $('.select-email[data-domain="' + domain + '"] option[value="'+email+'"]').attr('selected', 'selected');
                });
                $(document).on('click', '.btn-change-dcv', function (evt) {
                    var btn = evt.currentTarget;
                    var domain = $(btn).attr('data-domain');
                    var dcv = $('.select-dcv[data-domain="' + domain + '"]').val();
                    var email = $('.select-email[data-domain="' + domain + '"]').val();
                    $(btn).text('修改中..');
                    $(btn).attr('disabled', 'disabled');
                    $.ajax({
                        url: 'clientarea.php?action=productdetails&id={/literal}{$serviceid}{literal}',
                        type: 'POST',
                        data: {
                            domain: domain,
                            dcv: dcv,
                            email: dcv == 'email' ? email: null,
                            'mg-action': 'changeDcv',
                        },
                        dataType: 'JSON',
                        complete: function (json) {
                            $(btn).text('修改验证');
                            location.reload();
                        }
                    });
                });
                $(document).on('change', '.select-dcv', function (evt) {
                    var select = evt.currentTarget;
                    var domain = $(select).attr('data-domain');
                    if ($(select).val() === 'email') {
                        $('.select-email[data-domain="' + domain + '"]').css('display', 'inline-block');
                    } else {
                        $('.select-email[data-domain="' + domain + '"]').hide();
                    }
                });
            })(jQuery);
        {/literal}
    </script>
