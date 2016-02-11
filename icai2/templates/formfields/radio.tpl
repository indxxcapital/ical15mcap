<!--
{foreach from=$Form_Params.options key=k item=item}
	 
	<label><input style="width:10px" type="radio" name="{$Form_Params.name}" id="{$Form_Params.name}_{$k}" value="{$k}" class="{$Form_Params.class}"
    {if $Form_Params.value==$k} checked="checked"{/if}/> {$item}</label>
	{/foreach}

<span id="error_{$Form_Params.name}" {$Form_Params.errorClass}>{$Form_Params.errorMessage}</span>-->


<label class="control-label">{$formParams.feild_label}{if $formParams.is_required}<sup class="redalert">*</sup>{/if}</label>

{foreach from=$formParams.options key=k item=item}
	<label></label><div class="controls"><input style="width:10px" type="radio" name="{$formParams.feild_code}" id="{$formParams.feild_code}_{$k}" value="{$k}" class="{$formParams.class}" {if $formParams.value == $k} checked="checked"{/if} {$formParams.feildValues}/> {$item}</div>
	{/foreach}
    
    </span>

<span id="error_{$formParams.feild_code}" {$formParams.errorClass}>{$formParams.errorMessage}</span>
