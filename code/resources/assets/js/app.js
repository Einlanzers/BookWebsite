require("./bootstrap");

Vue.component("sample-search-row", require("./components/SampleSearchRow.vue"));

if (document.getElementById("sampleSearchForm"))
{
	const app = new Vue({
		el: "#sampleSearchForm",
		data: {
			fields: [],
			nextFieldId: 0,
			rowOptions: [],
			isLoading: false,
			searchFieldsParam: "",
			formAction: "",
		},
		methods: {
			submitSearchEvent: function ()
			{
				if (this.isLoading)
					return;

				this.formAction = window.location.origin + window.location.pathname;
				
				if (this.hasDuplicateInvalidField())
				{
					alert("Please only submit one filter per field.");
					return false;
				}
				
				this.searchFieldsParam = btoa(JSON.stringify(this.fields));
				this.$nextTick(function ()
				{
					$(this.$el).submit();
				});
			},
			exportEvent: function ()
			{
				if (this.isLoading)
					return;

				this.formAction = window.location.origin + window.location.pathname + "/export";
				
				if (this.hasDuplicateInvalidField())
				{
					alert("Please only submit one filter per field.");
					return false;
				}
				
				this.searchFieldsParam = btoa(JSON.stringify(this.fields));
				this.$nextTick(function ()
				{
					$(this.$el).submit();
				});
			},
			addFieldEvent: function ()
			{
				if (this.isLoading)
					return;

				this.addField();
			},
			removeFieldEvent: function (id)
			{
				if (this.isLoading)
					return;

				for (var i = 0; i < this.fields.length; i++)
				{
					if (this.fields[i].id == id)
					{
						this.fields.splice(i, 1);
						break;
					}
				}
			},
			childDataChangeEvent: function (id, data)
			{
				if (this.isLoading)
					return;
				
				var field = this.getFieldById(id);
				if (field)
				{
					field.field = data.searchField.id;
					field.value = data.searchValue;
					field.value2 = data.searchValue2;
				}
			},

			addField: function ()
			{
				this.fields.push(
				{
					id: this.nextFieldId,
					field: null,
					value: null,
					value2: null,
				});
				this.nextFieldId++;
			},
			getFieldById: function(id)
			{
				for (var i = 0; i < this.fields.length; i++)
					if (this.fields[i].id == id)
						return this.fields[i];
				return null;
			},
			parseUrlParameters: function()
			{
				this.isLoading = true;

				var queryString = "";
				var query = window.location.search.substring(1);
				var vars = query.split("&");
				for (var i = 0; i < vars.length; i++)
				{
					var pair = vars[i].split("=");
					if (pair[0] == "searchFields")
					{
						queryString = pair[1];
						break;
					}
				}

				if (queryString)
				{
					queryString = decodeURIComponent(queryString);
					queryString = atob(queryString);
					var fields = JSON.parse(queryString);
					for (var i = 0; i < fields.length; i++)
					{
						this.fields.push(
						{
							id: fields[i].id,
							field: fields[i].field,
							value: fields[i].value,
							value2: fields[i].value2,
						});
						this.nextFieldId = fields[i].id + 1;
					}
				}
				else
				{
					this.addField();
				}

				this.isLoading = false;
			},
			hasDuplicateInvalidField: function()
			{
				var exceptionFieldIds = [];
				for (var i = 0; i < this.rowOptions.length; i++)
				{
					var rowOption = this.rowOptions[i];
					if (rowOption.allow_multiple)
						exceptionFieldIds.push(rowOption.id);
				}
				
				var fieldIds = [];
				for (var i = 0; i < this.fields.length; i++)
				{
					var fieldId = this.fields[i].field;
					for (var u = 0; u < fieldIds.length; u++)
					{
						var innerFieldId = fieldIds[u];
						if (innerFieldId == fieldId)
						{
							var hasException = false;
							for (var o = 0; o < exceptionFieldIds.length; o++)
							{
								var exceptionFieldId = exceptionFieldIds[o];
								if (exceptionFieldId == innerFieldId)
								{
									hasException = true;
									break;
								}
							}
							if (!hasException)
								return true;
						}
					}
					fieldIds.push(fieldId);
				}
				return false;
			},
		},
		created: function()
		{
			var that = this;
			this.isLoading = true;

			axios.get("/api/sample-search-data")
			.then(function (response)
			{
				that.rowOptions = response.data;
				that.isLoading = false;

				that.parseUrlParameters();
			})
			.catch(function (error)
			{
				alert("There was an issue retrieving the search fields: " + error);
			});
		},
	});
}

if (document.getElementById("testSearchForm"))
{
	const app = new Vue({
		el: "#testSearchForm",
		data: {
			fields: [],
			nextFieldId: 0,
			rowOptions: [],
			isLoading: false,
			searchFieldsParam: "",
			formAction: "",
		},
		methods: {
			submitSearchEvent: function ()
			{
				if (this.isLoading)
					return;
				
				this.formAction = window.location.origin + window.location.pathname;
				
				if (this.hasDuplicateInvalidField())
				{
					alert("Please only submit one filter per field.");
					return false;
				}
				
				this.searchFieldsParam = btoa(JSON.stringify(this.fields));
				this.$nextTick(function ()
						{
					$(this.$el).submit();
						});
			},
			exportEvent: function ()
			{
				if (this.isLoading)
					return;
				
				this.formAction = window.location.origin + window.location.pathname + "/export";
				
				if (this.hasDuplicateInvalidField())
				{
					alert("Please only submit one filter per field.");
					return false;
				}
				
				this.searchFieldsParam = btoa(JSON.stringify(this.fields));
				this.$nextTick(function ()
				{
					$(this.$el).submit();
				});
			},
			addFieldEvent: function ()
			{
				if (this.isLoading)
					return;
				
				this.addField();
			},
			removeFieldEvent: function (id)
			{
				if (this.isLoading)
					return;
				
				for (var i = 0; i < this.fields.length; i++)
				{
					if (this.fields[i].id == id)
					{
						this.fields.splice(i, 1);
						break;
					}
				}
			},
			childDataChangeEvent: function (id, data)
			{
				if (this.isLoading)
					return;
				
				var field = this.getFieldById(id);
				if (field)
				{
					field.field = data.searchField.id;
					field.value = data.searchValue;
					field.value2 = data.searchValue2;
				}
			},
			
			addField: function ()
			{
				this.fields.push(
				{
					id: this.nextFieldId,
					field: null,
					value: null,
					value2: null,
				});
				this.nextFieldId++;
			},
			getFieldById: function(id)
			{
				for (var i = 0; i < this.fields.length; i++)
					if (this.fields[i].id == id)
						return this.fields[i];
				return null;
			},
			parseUrlParameters: function()
			{
				this.isLoading = true;
				
				var queryString = "";
				var query = window.location.search.substring(1);
				var vars = query.split("&");
				for (var i = 0; i < vars.length; i++)
				{
					var pair = vars[i].split("=");
					if (pair[0] == "searchFields")
					{
						queryString = pair[1];
						break;
					}
				}
				
				if (queryString)
				{
					queryString = decodeURIComponent(queryString);
					queryString = atob(queryString);
					var fields = JSON.parse(queryString);
					for (var i = 0; i < fields.length; i++)
					{
						this.fields.push(
								{
									id: fields[i].id,
									field: fields[i].field,
									value: fields[i].value,
									value2: fields[i].value2,
								});
						this.nextFieldId = fields[i].id + 1;
					}
				}
				else
				{
					this.addField();
				}
				
				this.isLoading = false;
			},
			hasDuplicateInvalidField: function()
			{
				var exceptionFieldIds = [];
				for (var i = 0; i < this.rowOptions.length; i++)
				{
					var rowOption = this.rowOptions[i];
					if (rowOption.allow_multiple)
						exceptionFieldIds.push(rowOption.id);
				}
				
				var fieldIds = [];
				for (var i = 0; i < this.fields.length; i++)
				{
					var fieldId = this.fields[i].field;
					for (var u = 0; u < fieldIds.length; u++)
					{
						var innerFieldId = fieldIds[u];
						if (innerFieldId == fieldId)
						{
							var hasException = false;
							for (var o = 0; o < exceptionFieldIds.length; o++)
							{
								var exceptionFieldId = exceptionFieldIds[o];
								if (exceptionFieldId == innerFieldId)
								{
									hasException = true;
									break;
								}
							}
							if (!hasException)
								return true;
						}
					}
					fieldIds.push(fieldId);
				}
				return false;
			},
		},
		created: function()
		{
			var that = this;
			this.isLoading = true;
			
			axios.get("/api/test-search-data")
			.then(function (response)
			{
				that.rowOptions = response.data;
				that.isLoading = false;
				
				that.parseUrlParameters();
			})
			.catch(function (error)
			{
				alert("There was an issue retrieving the search fields: " + error);
			});
		},
	});
}