{if $saved == 'true' }
	<meta http-equiv="refresh" content="2;URL=index.php?module=inventory&amp;view=manage" />
	<br />
	 {$LANG.save_cron_success}
	<br />
	<br />
{/if}
{if $saved == 'false' }
	<meta http-equiv="refresh" content="2;URL=index.php?module=inventory&amp;view=manage" />
	<br />
	 {$LANG.save_cron_failure}
	<br />
	<br />
{/if}

{if $saved ==false}
	{if $smarty.post.op == 'add' AND $smarty.post.product_id == ''} 
		<div class="validation_alert"><img src="./images/common/important.png" alt="" />
		You must select an product</div>
		<hr />
	{/if}


<form name="frmpost" action="index.php?module=inventory&view=add" method="POST" id="frmpost">
<br />	 

<table align="center">
	<tr>
		<td class="details_screen">{$LANG.product}</td>
		<td>
		<select name="product_id" class="validate[required]">
		    <option value=''></option>
			{foreach from=$product_all item=product}
				<option value="{$product.id}">{$product.description}</option>
			{/foreach}
		</select>
		</td>
	</tr>
	<tr>
		<td class="details_screen">{$LANG.quantity}</td>
		<td>
		<input class="validate[required]" name="quantity" size="10" >
     </tr>
    <tr wrap="nowrap">
            <td class="details_screen">{$LANG.date_upper}</td>
            <td wrap="nowrap">
                <input type="text" class="validate[required,custom[date],length[0,10]] date-picker" size="10" name="date" id="date" value='{"now"|date_format:"%Y-%m-%d"}' />   
            </td>
    </tr>
	<tr>
		<td class="details_screen">{$LANG.cost}</td>
		<td>
		<input name="cost" size="10" >
     </tr>
	<tr>
		<td class="details_screen">{$LANG.notes}</td>
		<td><textarea  name="note"  class="editor" rows="8" cols="50">{$customer.notes|unescape}</textarea></td>
	</tr>

</table>
<br />
<table class="buttons" align="center">
	<tr>
		<td>
			<button type="submit" class="positive" name="id" value="{$LANG.save}">
			    <img class="button_img" src="./images/common/tick.png" alt="" /> 
				{$LANG.save}
			</button>

			<input type="hidden" name="op" value="add" />
		
			<a href="./index.php?module=cron&view=manage" class="negative">
		        <img src="./images/common/cross.png" alt="" />
	        	{$LANG.cancel}
    		</a>
	
		</td>
	</tr>
</table>


</form>
{/if}