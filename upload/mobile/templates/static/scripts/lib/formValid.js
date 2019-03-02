;(function ($) {
  var rSelector = /^[#.]([\w-]+)/, //鍒ゆ柇鏄?惁鏄痠d鍜宑lass閫夋嫨鍣?紝鍋囧?鏄?氨鑾峰彇閫夋嫨鍣ㄥ唴瀹
        
      /*鏁扮粍鍜屽?璞＄殑鍘熷瀷鏂规硶锛岄伩鍏嶅?璞″?娆¤皟鐢ㄥ拰鏂规硶澶氭?鏌ユ壘*/
      Arr = Array,
      Obj = Object,
      slice = Arr.prototype.slice, 
      concat = Arr.prototype.concat,
      hasOwnProperty = Obj.prototype.hasOwnProperty,
      toString = Obj.prototype.toString;
   
  /*
   * @params target {object} 鎵╁睍鐨勭洰鏍囧?璞
   * @params source {object} 鎵╁睍鐨勬簮瀵硅薄
   * */
  function merge (target, source) {
    var args = slice.call(arguments),
        ride = typeof args[args.length - 1] === 'boolean' ? args.pop() : true,
        i = 1,
        prop;

    if (args.length === 1) {
      source = target;
      target = !root.window ? {} : root;
    }
    while((source = args[i++])) {
      for (prop in source) {
        if (hasOwnProperty.call(source, prop)) {
          (ride || !target[prop]) && (target[prop] = source[prop]);
        }
      }
    }
      return target;
  };

  /*鍒ゆ柇鏄?惁鏄?嚱鏁?/
  function isFunction (fn) {
    return !!(fn && toString.call(fn).slice(8, -1).toLowerCase() === 'function')
  }

  /*
   * @params tmplStr {string} 妯℃澘HTML浠ｇ爜
   * @params data {obj} 鏄犲皠妯℃澘鐨勬暟鎹
  */
  function getTemplate (tmplStr, data) {
    if (rSelector.test(tmplStr)) {
      tmplStr = $(tmplStr).html();
    }
    return tmplStr && tmplStr.replace(/\{([a-z]+)\}/i, function (val, v1) {
      return data[v1] || '';
    });
  };

  merge($.fn, {
    readyValid: function (config) {
      var fields = [],
          pauseMessage = false,
          item;

      /*杈撳嚭閿欒?淇℃伅(html鏍煎紡)*/
      function defaultErrMsg (classMsg) {
        var msgErrClass = classMsg || 'un-message';
        return '<span class="' + msgErrClass + '" role="page">璇疯緭鍏ュ繀瑕佺殑淇℃伅</span>';
      };

      /*鐢熸垚閿欒?淇℃伅(txt|html)*/
      function generateError(err) {
        if (err) {
          return getTemplate(err.template, err.data);
        }
        return defaultErrMsg();
      };

      /*鎻愪氦澶勭悊*/
      function handleSubmit () {
        var validErr = false, //鎻愪氦琛ㄥ崟鐨勬椂鍊欐槸鍚︽湁閿欒?
            i, l;

        
        for (i = 0, l = fields.length; i < l; i += 1) {
          if(!(fields[i].startValid()) && (validErr = true)) {
            break;
          } 
          //!(fields[i].startValid()) && (validErr = true);
        //  break;
        }

        if (validErr) {
          //isFunction(config.failure) && (config.failure());
          return false;
        }
        return isFunction(config.success) && (config.success());
      };

      /*blur澶勭悊*/
      function handleBlur (handleBlurEl) {
        handleBlurEl.startValid(); 
      };

      /*琛ㄥ崟瀛楁?楠岃瘉澶勭悊*/
      function validateField (opts, selector) {
        var field = $(selector),
            errorEl = null,
            fErrorEl = null;

            field.startValid = function () {
              var el = $(this),
                  error = false, //榛樿?闈炵┖楠岃瘉鎴愮珛,
                  fError = false, //榛樿?鏍煎紡鍖栨垚绔嬶紝
                  required = opts.required,
                  format = opts.format || [],
                  val = el.val(),
                  goFn, goExec, rErrClass,fErrClass, fData, template, fTemplate, sign, data, exec, i, l;


              (errorEl && errorEl.size() > 0) && (errorEl.remove());
              (fErrorEl && fErrorEl.size() > 0) && (fErrorEl.remove());
              //妫€鏌ュ湪handler鏄?惁鏈夐敊璇?骇鐢
              if (required.isSure && val.length === 0) {
                  error = true;
                  template = required.errTemplate;
                  rErrClass = required.errClass;
                  data = required.data;
                  exec = required.exec;
              }

              for (i = 0, l = format.length; i < l; i += 1) {
                sign = format[i].sign;
                goFn = isFunction(format[i].test) && format[i].test;
                
                if ((fError = !goFn(sign, val))) {
                  fErrClass = format[i].errClass;
                  fTemplate = format[i].errTemplate;

                  fData = format[i].data;
                  goExec = format[i].exec;
                  break;   
                }
              }
              //濡傛灉妫€娴嬪埌鏈夐敊璇?殑鏃跺€
              if (error) {             
                errorEl = $(generateError(template ? {template: template, data: data || {}} : null));
                exec(errorEl);
                return false;
              }

              else if (fError) {      
                fErrorEl = $(generateError({template: fTemplate, data: fData || {}}));
                goExec && goExec(fErrorEl);
                return false;
              }
              return true;     
            };
            
            field.bind(opts.then || 'blur', (function () {handleBlur(field)}));
            fields.push(field);
      };

      for (item in config.fields) {
        (hasOwnProperty.call(config.fields, item)) && (validateField(config.fields[item], item));
      }

      if (config.submitBtn) {
        $(config.submitBtn).click(handleSubmit);
      } else {
        this.bind('submit', handleSubmit);
      }

      return this;
    }
  });

})(this.jQuery || this.Zepto);