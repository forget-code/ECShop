
/*
 * 绫讳技backbone.js鐨勪簨浠剁?鐞嗛?鏍
*/

;(function (win) {

	var events = {};

	/*
	 * 涓€浜涘伐鍏锋柟娉
	*/
	var util = {
		isArray: function (arr) {
			return Object.prototype.toString
						 .call(arr).slice(8, -1)
						 .toLowerCase() === 'array';
		},

		isHTMLEle: function (ele) {
			return Object.prototype.toString
						 .call(ele).slice(8, -1)
						 .toLowerCase().indexOf('element') > 0;
		} 
	};
	
	/*
	 * 妯℃嫙jquery鐨勯€夋嫨鍣ㄣ€丏OM鎿嶄綔銆佷簨浠剁粦瀹 (鍑忓皯瀵筳query浠ュ強zepto鐨勪緷璧?
	*/

	//閫夋嫨鍗曚釜DOM鍏冪礌
	var $ = function (selector) {
		return document.querySelector 
					 ? document.querySelector.call(document, selector)
					 : document.getElementById(selector);
	};

	//閫夋嫨澶氫釜DOM鍏冪礌
	var $All = function (selector) {
		return document.querySelectorAll(selector);
	};

	//DOM鍏冪礌灞炴€ф搷浣
	var $attr = function (el, attr, val) {
		var hasOwnProperty = Object.prototype.hasOwnProperty,
				prop;

		if (!el || typeof el === 'string') throw new Error('璇蜂紶鍏ヤ竴涓狣OM瀵硅薄');
		if (typeof attr === 'object') {
			for (prop in attr) {
				hasOwnPrototype.call(attr, prop) && ($attr(el, prop, attr[prop]));
			}
		}
		else if (val) {
			el.setAttribute(attr, val);
		}
		else {
			return el[attr] ? el[attr] : el.getAttribute(attr);
		}
	};

	//DOM鍏冪礌琛屽唴鏍峰紡鎿嶄綔
	var $css = function (el, type, val) {
		var hasOwnProperty = Object.prototype.hasOwnProperty,
				prop;

		if (!el || typeof el === 'string') throw new Error('璇蜂紶鍏ヤ竴涓狣OM瀵硅薄');
		if (typeof type === 'object') {
			for (prop in type) {
				hasOwnPrototype.call(type, prop) && ($attr(el, prop, type[prop]));
			}
		}
		else if (val) {
			el.style[type] = val;
		}
		else {
			return el.style[type];
		}
	};

	var $classOption = function (el, className, flag) {
		var flag = flag || 'add';

		if (!el || typeof el === 'string') throw new Error('璇蜂紶鍏ヤ竴涓狣OM瀵硅薄');
		el.classList[flag](className);
	};

	/*
	 * 浜嬩欢绠＄悊绫
	 * @params eventConfig{object} 
	 * @params return nothings
	*/
	function Events (eventObj) {
		if (!(this instanceof Events)) return new Events(eventObj);
		if (!events) throw new Error('浼犲叆涓€涓猠vent obj');
		this.injection(eventObj);
	}

	Events.prototype = {
		
		//娉ㄥ叆浜嬩欢	
		injection: function (eventObj) {
			var hasOwnProperty = Object.prototype.hasOwnProperty,
			  	eventName, selector, prop;
				
			for (prop in eventObj) {
				if (hasOwnProperty.call(eventObj, prop)) {
					eventName = prop.split(' ')[0];
					selector = /[.#]/.test(prop.split(' ')[1]) ? prop.split(' ')[1] : '.' + prop.split(' ')[1];
					(!events[eventName]) && (events[eventName] = []);
					events[eventName].push({
						'selectorName': selector,
						'selector': $(selector),
						'handle': eventObj[prop]
					});
				}
			}

			return this;	
		},

		//鍒犻櫎浜嬩欢
		remove: function (eventName) {
			events[eventName] && (delete events[eventName]);
		},

		//瑙﹀彂浜嬩欢
		fire: function (e, obj, handle) {
			handle(e, obj, Array.prototype.slice(arguments, 3));
		},

		//浜嬩欢缁戝畾鐩稿簲鐨勫厓绱
		bind: function (eventName) {
			var eventArr = events[eventName],
				  self = this,
				  args = Array.prototype.slice.call(arguments, 1),
				  handle, i, len, el;

		  if (!eventArr) throw new Error('涓嶅瓨鍦ㄧ殑浜嬩欢');
		
		  for (i = 0, len = eventArr.length; i < len, el = eventArr[i++];) {
		  	if (el) {
		  		(function (handle) {

			  			el['selector'].addEventListener(eventName, function (e) {
			  			self.fire.apply(null, [e, this, handle].concat(args));
		  			}, false);

		  		})(el['handle']);	
		  	}
		  }

		  return this;
		}
	}

	win.Events = Events;

})(window);