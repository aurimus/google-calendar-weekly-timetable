<table class="form-table colors" >
	<tr v-for="(colors, name, index) in events" :class="{last_item: index == Object.keys(events).length - 1}">
		<td :style="{'background-color': colors.bcolor.hex, color: text_color}">{{name}}</td>
		<td class="vue-color-picker">
			<slider-picker v-model="colors.bcolor" />
		</td>
		<td><input type="color" v-model="colors.bcolor.hex"/></td>
	</tr>
</table>
<input class="button-primary" type="submit" @click="updateColors" value="Update"/>
