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
		<div class="col-xs-12 explication-container">
			<h3> {l s='How to send data to a profile of Icommkt' mod='icommktconnector'} </h3>
			<h4>{l s='Data you need:' mod='icommktconnector'}</h4>
			<ul class="list-data-need">
				<li class="li-data">
					<strong>{l s='Api Key:' mod='icommktconnector'}</strong>
					{l s='Code obtained from the account of Icommkt' mod='icommktconnector'}
				</li>
				<ul class="sub-list-data">
					<li class="li-data">{l s='You can get it by clicking on the top gear -> my account' 
					mod='icommktconnector'}</li>
				</ul>	
				<li class="li-data">
					<strong>{l s='Profile key:' mod='icommktconnector'}</strong>
					{l s='Code obtained from the account profile where the data will be sent' 
					mod='icommktconnector'}
				</li>
				<ul class="sub-list-data">
					<li class="li-data">{l s='We look for our profile, click on the arrow and click on profile key' 
					mod='icommktconnector'}</li>
				</ul>	
			</ul>
			<h4> {l s='Fields to Abandon cart' mod='icommktconnector'} </h4>
			<ul>
				<li>{l s='API Key' mod='icommktconnector'}</li>
				<li>{l s='Profile Key Cart Abandon' mod='icommktconnector'}</li>
				<li>{l s='Days to abandon' mod='icommktconnector'}</li>
				<li>{l s='Secure TOKEN' mod='icommktconnector'}</li>
			</ul>
			<h4> {l s='Fields to send newsletter users' mod='icommktconnector'} </h4>
			<ul>
				<li>{l s='API Key' mod='icommktconnector'}</li>
				<li>{l s='Profile Key' mod='icommktconnector'}</li>
				<li>{l s='Secure TOKEN' mod='icommktconnector'}</li>
			</ul>
		</div>

		<div class="col-xs-12 url-container">
			<h3>{l s='Example URLs' mod='icommktconnector'}</h3>
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
			<h5>{l s= 'Send newsletter users' mod='icommktconnector'}</h5>
			<div class="">
				<span>{html_entity_decode($send_icommkt_user|escape:'htmlall':'UTF-8')}</span>
			</div>
		</div>
	</div>
	<hr />
</div>
