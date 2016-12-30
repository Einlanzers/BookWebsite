<template>
	<div class="form-group" style="margin-top:3px;">
		<select v-model="searchField" class="form-control input-sm" style="width:200px;">
			<option v-for="option in options" v-bind:value="option">{{option.value}}</option>
		</select>
		<span v-if="searchField.type == 'number'">
			<input v-model="searchValue" v-on:keyup.enter="$emit('submit-event')" class="form-control input-sm" placeholder="Minimum" style="width:146px;" type="text" />
			<input v-model="searchValue2" v-on:keyup.enter="$emit('submit-event')" class="form-control input-sm" placeholder="Maximum" style="width:146px;" type="text" />
		</span>
		<input v-if="searchField.type == 'text'" v-model="searchValue" v-on:keyup.enter="$emit('submit-event')" class="form-control input-sm" placeholder="Search Term" style="width:296px;" type="text" />
		<span v-if="searchField.type == 'date'">
			<input v-model="searchValue" v-on:keyup.enter="$emit('submit-event')" class="form-control input-sm datepicker-event" placeholder="Minimum" style="width:146px;" type="text" />
			<input v-model="searchValue2" v-on:keyup.enter="$emit('submit-event')" class="form-control input-sm datepicker-event" placeholder="Maximum" style="width:146px;" type="text" />
		</span>
		<select v-if="searchField.type == 'bool'" v-model="searchValue" class="form-control input-sm" style="width:296px;">
			<option v-for="option in boolOptions" v-bind:value="option">{{option.value}}</option>
		</select>
		<select v-if="searchField.type == 'select'" v-model="searchValue" class="form-control input-sm" style="width:296px;">
			<option v-for="option in searchField.options" v-bind:value="option">{{option.value}}</option>
		</select>

		<a v-if="id != 0" class="btn btn-danger btn-sm" v-on:click="$emit('remove-field-event', id)" v-bind:disabled="isLoading"><i class="fa fa-minus"></i></a>
		<a v-if="id == 0" class="btn btn-success btn-sm" v-on:click="$emit('add-field-event')" v-bind:disabled="isLoading"><i class="fa fa-plus"></i></a>

		<a v-if="id == 0" class="btn btn-primary btn-sm" v-on:click="$emit('submit-event')" v-bind:disabled="isLoading">Filter</a>
		<a v-if="id == 0" class="btn btn-primary btn-sm" v-on:click="$emit('export-event')" v-bind:disabled="isLoading">Export</a>
	</div>
</template>

<script>
	module.exports = {
		props: ["id", "options", "isLoading", "field", "value", "value2"],
		data: function()
		{
			return {
				searchField: null,
				searchValue: null,
				searchValue2: null,
				boolOptions: [
					{id: null, value: "(None)"},
					{id: 1, value: "Yes"},
					{id: 0, value: "No"},
				],
			}
		},
		watch: {
			searchField: function(val, oldVal)
			{
				this.$emit("data-change-event", this.id, this.$data);

				// Don't wipe values if this is the first time searchField was set (on load)
				if (oldVal != null)
				{
					if (val.type == "select" && val.options && val.options.length > 0)
						this.searchValue = val.options[0];
					else if (val.type == "bool")
						this.searchValue = this.boolOptions[0];
					else
						this.searchValue = null;

					this.searchValue2 = null;
				}
			},
			searchValue: function(val, oldVal)
			{
				this.$emit("data-change-event", this.id, this.$data);
			},
			searchValue2: function(val, oldVal)
			{
				this.$emit("data-change-event", this.id, this.$data);
			},
		},
		created: function()
		{
			if (this.field)
			{
				for (var i = 0; i < this.options.length; i++)
				{
					if (this.options[i].id == this.field)
					{
						this.searchField = this.options[i];
						break;
					}
				}
			}
			else
			{
				this.searchField = this.options[0];
			}

			this.searchValue = this.value;
			this.searchValue2 = this.value2;

			var that = this;
			if (that.searchField && that.searchField.type == "date")
			{
				that.$nextTick(function ()
				{
					$(that.$el).find(".datepicker-event").datepicker({autoclose: true}).on("changeDate", function(e)
					{
						if ($(this).prop("placeholder") == "Maximum")
						{
							that.searchValue2 = $(this).val();
						}
						else
						{
							that.searchValue = $(this).val();
						}
					});
				});
			}
		},
		updated: function()
		{
			var that = this;
			if (that.searchField && that.searchField.type == "date")
			{
				$(that.$el).find(".datepicker-event").datepicker({autoclose: true}).on("changeDate", function(e)
				{
					if ($(this).prop("placeholder") == "Maximum")
					{
						that.searchValue2 = $(this).val();
					}
					else
					{
						that.searchValue = $(this).val();
					}
				});
			}
			else
			{
				$(that.$el).find(".form-control").datepicker("remove");
			}
		},
	}
</script>
