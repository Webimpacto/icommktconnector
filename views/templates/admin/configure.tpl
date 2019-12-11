{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
* @author    Icommkt
* @copyright Icommkt
* @license   GPLv3
*
*}

<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-5 text-right">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/logo.jpg" />
		</div>
		<div class="col-xs-7 text-left">
			<h2>{l s='ICOMMKT' mod='icommktconnector'}</h2>
			<h4>{l s='Email & Automation Marketing' mod='icommktconnector'}</h4>
		</div>

		<div class="col-xs-12 url-container">
			<h4>{l s='Example URLs' mod='icommktconnector'}</h4>
			{if isset($load_cart_url) && !empty($load_cart_url)}
				<div class="url-load-cart">
					<span><strong>{l s='Load cart' mod='icommktconnector'}</strong>:</span>
					<span>{html_entity_decode($load_cart_url|escape:'htmlall':'UTF-8')}</span>
				</div>
			{/if}
			{if isset($send_abandoment_cart_url) && !empty($send_abandoment_cart_url)}
				<div class="url-send-abandoment-cart">
					<span><strong>{l s='Send abandoment cart' mod='icommktconnector'}</strong>:</span>
					<span>{html_entity_decode($send_abandoment_cart_url|escape:'htmlall':'UTF-8')}</span>
				</div>
			{/if}
		</div>
		<div class="col-xs-12 crons-container">
			<h4>{l s='Example Crons' mod='icommktconnector'}</h4>
			{if isset($send_abandoment_cart_url) && !empty($send_abandoment_cart_url)}
				<div class="url-send-abandoment-cart">
					<span>{html_entity_decode($send_abandoment_cart_url|escape:'htmlall':'UTF-8')}</span>
				</div>
			{/if}
			<h5>{l s= 'Send user to icommkt' mod='icommktconnector'}</h5>
			<div class="">
				<span>{html_entity_decode($send_icommkt_user|escape:'htmlall':'UTF-8')}</span>
			</div>
		</div>
	</div>
	<hr />
</div>
