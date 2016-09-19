		{if:label} 	<label for='%%id%%'>%%label%%</label> {/if:label} 
		<div class="input number %%name%%">{if:before_input} %%before_input%% {/if:before_input}
			<input type="number"
				id="%%id%%"
				name="%%name%%"
				value="%%value%%" 
 				{if:min} min="%%min%%" {/if:min}
 				{if:max} max="%%max%%" {/if:max}
				placeholder="%%placeholder%%" 
				{if:inputclass}class="%%inputclass%%"{/if:inputclass} 
			/></div>
		{if:default} <div class='default'>%%default%%</div> {/if:default}
