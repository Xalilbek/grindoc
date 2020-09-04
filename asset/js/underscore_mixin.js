_.mixin({

	toFixed: (number, precision = 2) => {
		return number % 1 ? number.toFixed(precision) : number;
	},

	capitalizeFirstChar: (string) => {
		return string.charAt(0).toUpperCase() + string.substring(1)
	},

	ifEmpty: (text, alterString = '') => {
		if (_.isEmpty(text)) {
			return alterString;
		}

		return text ? text : alterString;
	},

	getIfNotNull: (objectOrNull, property, altString = '') => {
		if (_.isNull(objectOrNull)) {
			return altString;
		}

		return objectOrNull[property];
	},

	/**
	 * @param obj
	 * @param {array} params list of required parameters
	 * @throws Error
	 */
	checkRequiredProps: (obj, params) => {
		params.forEach((param) => {
			if (!obj.hasOwnProperty(param)) {
				throw new Error(`Ommited required "${param}" param `);
			}
		});
	},

	encodeURIComponent: (obj) => {
		return Object.keys(obj).map(function (key) {
			return [key, obj[key]].map(encodeURIComponent).join("=");
		}).join("&");
	}

});