/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./includes/builders/gutenberg/src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./includes/builders/gutenberg/src/blocks/ywsbs-plan/edit.js":
/*!*******************************************************************!*\
  !*** ./includes/builders/gutenberg/src/blocks/ywsbs-plan/edit.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _components_title_plan__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../components/title-plan */ "./includes/builders/gutenberg/src/components/title-plan.js");











function EditorPlan(props) {
  var attributes = props.attributes,
      setAttributes = props.setAttributes;
  var className = props.className;
  className = className + (typeof animation !== 'undefined' && animation !== '' ? ' ' + animation : '');

  var onSelectImage = function onSelectImage(_ref) {
    var id = _ref.id,
        url = _ref.url,
        alt = _ref.alt;
    props.setAttributes({
      id: id,
      url: url,
      alt: alt
    });
  };

  var onSelectURL = function onSelectURL(url) {
    props.setAttributes({
      url: url,
      id: null,
      alt: ""
    });
  };

  var onChangeColor = function onChangeColor(value, type) {
    if (typeof value !== 'undefined') {
      if (type === 'backgroundColor') {
        setAttributes({
          backgroundColor: value
        });
      }

      if (type === 'borderColor') {
        setAttributes({
          borderColor: value
        });
      }

      if (type === 'titleBackgroundColor') {
        setAttributes({
          titleBackgroundColor: value
        });
      }

      if (type === 'titleColor') {
        setAttributes({
          titleColor: value
        });
      }

      if (type === 'subtitleColor') {
        setAttributes({
          subtitleColor: value
        });
      }

      if (type === 'textColor') {
        setAttributes({
          textColor: value
        });
      }
    }
  };

  var updateAttributes = function updateAttributes(value, name) {
    if (name === 'borderRadius') {
      setAttributes({
        borderRadius: value
      });
    }

    if (name === 'title') {
      setAttributes({
        title: value
      });
    }
  };

  var backgroundColor = attributes.backgroundColor,
      borderColor = attributes.borderColor,
      borderRadius = attributes.borderRadius,
      titleAlign = attributes.titleAlign,
      titleFontSize = attributes.titleFontSize,
      textColor = attributes.textColor;
  var shadowColor = attributes.shadowColor,
      shadowH = attributes.shadowH,
      shadowV = attributes.shadowV,
      shadowBlur = attributes.shadowBlur,
      shadowSpread = attributes.shadowSpread;
  var subtitleFontSize = attributes.subtitleFontSize;
  var fontSizes = [{
    name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Small'),
    slug: 'small',
    size: 11
  }, {
    name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Medium'),
    slug: 'small',
    size: 13
  }, {
    name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Big'),
    slug: 'big',
    size: 40
  }];

  var isGradient = function isGradient(color) {
    var _props$attributes$bac, _props$attributes$bac2;

    var isLinear = (_props$attributes$bac = props.attributes.backgroundColor) === null || _props$attributes$bac === void 0 ? void 0 : _props$attributes$bac.includes('linear-gradient');
    if (isLinear) return true;
    var isRadial = (_props$attributes$bac2 = props.attributes.backgroundColor) === null || _props$attributes$bac2 === void 0 ? void 0 : _props$attributes$bac2.includes('radial-gradient');
    if (isRadial) return true;
    return false;
  };

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["BlockControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["AlignmentToolbar"], {
    value: titleAlign,
    onChange: function onChange(nextAlign) {
      setAttributes({
        titleAlign: nextAlign
      });
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Settings', 'yith-woocommerce-subscription')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show image', 'yith-woocommerce-subscription'),
    help: props.attributes.showImage ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show image', 'yith-woocommerce-subscription') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Hide image', 'yith-woocommerce-subscription'),
    checked: props.attributes.showImage,
    onChange: function onChange(value) {
      return props.onShowBlock(value, 'core/image');
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show list', 'yith-woocommerce-subscription'),
    help: props.attributes.showList ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show list', 'yith-woocommerce-subscription') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Hide list', 'yith-woocommerce-subscription'),
    checked: props.attributes.showList,
    onChange: function onChange(value) {
      return props.onShowBlock(value, 'core/list');
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('General Settings', 'yith-woocommerce-subscription')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["RangeControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Border Radius', 'yith-woocommerce-subscription'),
    value: props.attributes.borderRadius,
    onChange: function onChange(value) {
      return updateAttributes(value, 'borderRadius');
    },
    min: 0,
    max: 100
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["SelectControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Animated hover effect', 'yith-woocommerce-subscription'),
    value: props.attributes.animation,
    options: [{
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('No Effects', 'yith-woocommerce-subscription'),
      value: ''
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Grow', 'yith-woocommerce-subscription'),
      value: 'grow'
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Float', 'yith-woocommerce-subscription'),
      value: 'float'
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Sink', 'yith-woocommerce-subscription'),
      value: 'sink'
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shrink', 'yith-woocommerce-subscription'),
      value: 'shrink'
    }],
    onChange: function onChange(animation) {
      return setAttributes({
        animation: animation
      });
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["__experimentalPanelColorGradientSettings"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('General Color Settings', 'yith-woocommerce-subscription'),
    initialOpen: false,
    settings: [{
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Background Color', 'yith-woocommerce-subscription'),
      onColorChange: function onColorChange(color) {
        return onChangeColor(color, 'backgroundColor');
      },
      colorValue: props.attributes.backgroundColor,
      gradientValue: isGradient(props.attributes.backgroundColor) ? props.attributes.backgroundColor : '',
      onGradientChange: function onGradientChange(color) {
        return onChangeColor(color, 'backgroundColor');
      }
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Text Color'),
      onColorChange: function onColorChange(color) {
        return onChangeColor(color, 'textColor');
      },
      colorValue: props.attributes.textColor
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Border Color', 'yith-woocommerce-subscription'),
      onColorChange: function onColorChange(color) {
        return onChangeColor(color, 'borderColor');
      },
      colorValue: props.attributes.borderColor
    }]
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["__experimentalPanelColorGradientSettings"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Titles Bar', 'yith-woocommerce-subscription'),
    initialOpen: false,
    settings: [{
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Text Color'),
      onColorChange: function onColorChange(color) {
        return onChangeColor(color, 'titleColor');
      },
      colorValue: props.attributes.titleColor
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Subtitle Color'),
      onColorChange: function onColorChange(color) {
        return onChangeColor(color, 'subtitleColor');
      },
      colorValue: props.attributes.subtitleColor
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Background Color'),
      onColorChange: function onColorChange(color) {
        return onChangeColor(color, 'titleBackgroundColor');
      },
      colorValue: props.attributes.titleBackgroundColor,
      gradientValue: isGradient(props.attributes.titleBackgroundColor) ? props.attributes.titleBackgroundColor : '',
      onGradientChange: function onGradientChange(color) {
        return onChangeColor(color, 'titleBackgroundColor');
      }
    }]
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Set a transparent background color', 'yith-woocommerce-subscription'),
    checked: props.attributes.titleBackgroundColorTransparent,
    onChange: function onChange(value) {
      return props.setAttributes({
        titleBackgroundColorTransparent: value
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("h4", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Title font size', 'yith-woocommerce-subscription')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["FontSizePicker"], {
    fontSizes: fontSizes,
    value: titleFontSize || 20,
    fallbackFontSize: 20,
    withSlider: true,
    onChange: function onChange(newFontSize) {
      setAttributes({
        titleFontSize: newFontSize
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Subtitle Text', 'yith-woocommerce-subscription'),
    value: props.attributes.subtitleLabel,
    onChange: function onChange(value) {
      return setAttributes({
        subtitleLabel: value
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("h4", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Subtitle font size', 'yith-woocommerce-subscription')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["FontSizePicker"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show separator between subtitle and title', 'yith-woocommerce-subscription'),
    fontSizes: fontSizes,
    value: subtitleFontSize || 20,
    fallbackFontSize: 20,
    withSlider: true,
    onChange: function onChange(newFontSize) {
      setAttributes({
        subtitleFontSize: newFontSize
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show separator between subtitle and title', 'yith-woocommerce-subscription'),
    help: props.attributes.showSubtitleSeparator ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show separator', 'yith-woocommerce-subscription') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Hide separator', 'yith-woocommerce-subscription'),
    checked: props.attributes.showSubtitleSeparator,
    onChange: function onChange(value) {
      return props.setAttributes({
        showSubtitleSeparator: value
      });
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["__experimentalPanelColorGradientSettings"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Box Shadow', 'yith-woocommerce-subscription'),
    initialOpen: false,
    settings: [{
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shadow color', 'yith-woocommerce-subscription'),
      onColorChange: function onColorChange(color) {
        return setAttributes({
          shadowColor: color
        });
      },
      value: shadowColor,
      colorValue: props.attributes.shadowColor
    }]
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["RangeControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shadow H offset', 'yith-woocommerce-subscription'),
    value: shadowH || '',
    onChange: function onChange(value) {
      return setAttributes({
        shadowH: value
      });
    },
    min: -50,
    max: 50
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["RangeControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shadow V offset', 'yith-woocommerce-subscription'),
    value: shadowV || '',
    onChange: function onChange(value) {
      return setAttributes({
        shadowV: value
      });
    },
    min: -50,
    max: 50
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["RangeControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shadow blur', 'yith-woocommerce-subscription'),
    value: shadowBlur || '',
    onChange: function onChange(value) {
      return setAttributes({
        shadowBlur: value
      });
    },
    min: 0,
    max: 50
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["RangeControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Shadow spread', 'yith-woocommerce-subscription'),
    value: shadowSpread || '',
    onChange: function onChange(value) {
      return setAttributes({
        shadowSpread: value
      });
    },
    min: 0,
    max: 50
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: className,
    style: {
      color: textColor,
      background: backgroundColor,
      borderColor: borderColor,
      borderRadius: borderRadius,
      boxShadow: "".concat(shadowH, "px ").concat(shadowV, "px ").concat(shadowBlur, "px ").concat(shadowSpread, "px ").concat(shadowColor)
    }
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_components_title_plan__WEBPACK_IMPORTED_MODULE_8__["default"], {
    attributes: props.attributes,
    subtitleLabel: props.attributes.subtitleLabel,
    updateAttribute: updateAttributes
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["InnerBlocks"], {
    templateInsertUpdatesSelection: false,
    __experimentalCaptureToolbars: false,
    template: [['yith/ywsbs-price'], ['core/paragraph', {
      placeholder: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.'
    }], ['core/button', {
      value: 'Subscribe'
    }]],
    templateLock: "insert"
  })));
}

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_6__["compose"])([
/*	withSelect( (select, ownProps ) => {
		const {clientId} = ownProps;
		const parentClientId = select( 'core/block-editor' ).getBlockHierarchyRootClientId( clientId ); //Pass Child's Client Id.
		const parentAttributes = select('core/block-editor').getBlockAttributes( parentClientId );
		return {
			featuredLabel: parentAttributes.featuredLabel
		};
	}),*/
Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["withDispatch"])(function (dispatch, ownProps, registry) {
  return {
    onShowBlock: function onShowBlock(show, blockType) {
      var clientId = ownProps.clientId,
          setAttributes = ownProps.setAttributes;

      var _dispatch = dispatch('core/block-editor'),
          replaceInnerBlocks = _dispatch.replaceInnerBlocks;

      var _registry$select = registry.select('core/block-editor'),
          getBlocks = _registry$select.getBlocks;

      var innerBlocks = getBlocks(clientId);
      var newInnerBlocks = [];

      if (show) {
        switch (blockType) {
          case 'core/image':
            newInnerBlocks = [Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])(blockType)].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(innerBlocks));
            break;

          case 'core/list':
            innerBlocks.forEach(function (b) {
              newInnerBlocks.push(b);

              if (b.name === 'core/paragraph') {
                newInnerBlocks.push(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["createBlock"])(blockType));
              }
            });
            break;
        }
      } else {
        innerBlocks.forEach(function (block) {
          if (block.name !== blockType) {
            newInnerBlocks.push(block);
          }
        });
      }

      switch (blockType) {
        case 'core/image':
          setAttributes({
            showImage: show
          });
          break;

        case 'core/list':
          setAttributes({
            showList: show
          });
          break;
      }

      replaceInnerBlocks(clientId, newInnerBlocks, false);
    }
  };
})])(EditorPlan));

/***/ }),

/***/ "./includes/builders/gutenberg/src/blocks/ywsbs-plan/index.js":
/*!********************************************************************!*\
  !*** ./includes/builders/gutenberg/src/blocks/ywsbs-plan/index.js ***!
  \********************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _common__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../common */ "./includes/builders/gutenberg/src/common.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./edit */ "./includes/builders/gutenberg/src/blocks/ywsbs-plan/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./save */ "./includes/builders/gutenberg/src/blocks/ywsbs-plan/save.js");


function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }






var blockConfig = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Subscription Plan', 'yith-woocommerce-subscription'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add subscription table column', 'yith-woocommerce-subscription'),
  icon: _common__WEBPACK_IMPORTED_MODULE_3__["yith_icon"],
  category: 'yith-blocks',
  parent: ['yith/ywsbs-plans'],
  attributes: _common__WEBPACK_IMPORTED_MODULE_3__["attributesPlan"],
  edit: _edit__WEBPACK_IMPORTED_MODULE_4__["default"],
  save: _save__WEBPACK_IMPORTED_MODULE_5__["default"]
};
Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__["registerBlockType"])('yith/ywsbs-plan', _objectSpread({}, blockConfig));

/***/ }),

/***/ "./includes/builders/gutenberg/src/blocks/ywsbs-plan/save.js":
/*!*******************************************************************!*\
  !*** ./includes/builders/gutenberg/src/blocks/ywsbs-plan/save.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return save; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

function save(props) {
  var attributes = props.attributes;
  var title = attributes.title,
      textColor = attributes.textColor,
      titleAlign = attributes.titleAlign,
      titleBackgroundColor = attributes.titleBackgroundColor,
      titleColor = attributes.titleColor,
      titleFontSize = attributes.titleFontSize,
      titleBackgroundColorTransparent = attributes.titleBackgroundColorTransparent,
      backgroundColor = attributes.backgroundColor,
      borderColor = attributes.borderColor,
      borderRadius = attributes.borderRadius,
      animation = attributes.animation;
  var shadowColor = attributes.shadowColor,
      shadowH = attributes.shadowH,
      shadowV = attributes.shadowV,
      shadowBlur = attributes.shadowBlur,
      shadowSpread = attributes.shadowSpread;
  var subtitleColor = attributes.subtitleColor,
      subtitleLabel = attributes.subtitleLabel,
      subtitleFontSize = attributes.subtitleFontSize,
      showSubtitleSeparator = attributes.showSubtitleSeparator;
  var subtitleClass = 'subtitlePlan' + (showSubtitleSeparator ? ' with-separator' : '');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: animation,
    style: {
      color: textColor,
      background: backgroundColor,
      borderColor: borderColor,
      borderRadius: borderRadius,
      boxShadow: "".concat(shadowH, "px ").concat(shadowV, "px ").concat(shadowBlur, "px ").concat(shadowSpread, "px ").concat(shadowColor)
    }
  }, subtitleLabel !== '' ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("style", null, ".wp-block-yith-ywsbs-plan .subtitlePlan.with-separator:after { border-color: ".concat(subtitleColor, " }")) : '', Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "plan-title",
    style: titleBackgroundColorTransparent ? {
      background: 'transparent'
    } : {
      background: titleBackgroundColor
    }
  }, subtitleLabel !== '' ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: subtitleClass,
    style: {
      color: subtitleColor,
      fontSize: subtitleFontSize
    }
  }, subtitleLabel) : '', Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["RichText"].Content, {
    className: "ywsbs-plan__title",
    tagName: "h2",
    value: title,
    style: {
      textAlign: titleAlign,
      color: titleColor,
      fontSize: titleFontSize
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["InnerBlocks"].Content, null));
}

/***/ }),

/***/ "./includes/builders/gutenberg/src/blocks/ywsbs-plans/edit.js":
/*!********************************************************************!*\
  !*** ./includes/builders/gutenberg/src/blocks/ywsbs-plans/edit.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_8__);









var ALLOWED_BLOCKS = ['yith/ywsbs-plan'];

function PlansEditorContainer(props) {
  if (props.attributes.preview) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("img", {
      src: ywsbs_plans_object.ywsbs_plans_preview
    }));
  }

  var attributes = props.attributes,
      setAttributes = props.setAttributes,
      className = props.className,
      isSelected = props.isSelected,
      clientId = props.clientId,
      updatePlans = props.updatePlans,
      updatePlansAttributes = props.updatePlansAttributes,
      newplans = props.newplans,
      updateLabel = props.updateLabel;
  var plans = attributes.plans,
      planTemplate = attributes.planTemplate,
      subtitleLabel = attributes.subtitleLabel;
  var wrapperClass = className + ' ywsbs-plans ' + ' ywsbs-plans-' + newplans;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('General Settings', 'yith-woocommerce-subscription')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["RangeControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Number of Plans', 'yith-woocommerce-subscription'),
    value: newplans,
    onChange: function onChange(value) {
      return updatePlans(newplans, value);
    },
    min: 1,
    max: 5
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: wrapperClass
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["InnerBlocks"], {
    allowedBlocks: ALLOWED_BLOCKS,
    template: planTemplate,
    templateInsertUpdatesSelection: true,
    __experimentalCaptureToolbars: false,
    renderAppender: false,
    __experimentalMoverDirection: "horizontal"
  })));
}

var PlansEditContainerWrapper = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_8__["compose"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withSelect"])(function (select, ownProps) {
  var clientId = ownProps.clientId;
  return {
    newplans: select('core/block-editor').getBlocks(clientId).length
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["withDispatch"])(function (dispatch, ownProps, registry) {
  return {
    updateLabel: function updateLabel(newLabel) {
      var clientId = ownProps.clientId,
          setAttributes = ownProps.setAttributes;

      var _registry$select = registry.select('core/block-editor'),
          getBlocks = _registry$select.getBlocks;

      var innerBlocks = getBlocks(clientId);
      innerBlocks.forEach(function (block) {
        dispatch('core/editor').updateBlockAttributes(block.clientId, {
          subtitleLabel: ownProps.attributes.subtitleLabel
        });
      });
      setAttributes({
        subtitleLabel: newLabel
      });
    },
    updatePlans: function updatePlans(previousColumns, newColumns) {
      var clientId = ownProps.clientId,
          setAttributes = ownProps.setAttributes,
          isSelected = ownProps.isSelected;

      var _dispatch = dispatch('core/block-editor'),
          replaceInnerBlocks = _dispatch.replaceInnerBlocks,
          selectionChange = _dispatch.selectionChange;

      var _registry$select2 = registry.select('core/block-editor'),
          getBlocks = _registry$select2.getBlocks;

      var innerBlocks = getBlocks(clientId);
      newColumns = newColumns > 5 ? 5 : newColumns;
      var isAddingColumn = newColumns > previousColumns;

      if (isAddingColumn) {
        innerBlocks = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(innerBlocks), _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(Object(lodash__WEBPACK_IMPORTED_MODULE_7__["times"])(newColumns - previousColumns, function () {
          return Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__["createBlock"])('yith/ywsbs-plan');
        })));
      } else {
        // The removed column will be the last of the inner blocks.
        innerBlocks = Object(lodash__WEBPACK_IMPORTED_MODULE_7__["dropRight"])(innerBlocks, previousColumns - newColumns);
      }

      setAttributes({
        plans: newColumns
      });
      replaceInnerBlocks(clientId, innerBlocks, false);
    }
  };
})])(PlansEditorContainer);

var PlansEdit = function PlansEdit(props) {
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(PlansEditContainerWrapper, props);
};

/* harmony default export */ __webpack_exports__["default"] = (PlansEdit);

/***/ }),

/***/ "./includes/builders/gutenberg/src/blocks/ywsbs-plans/index.js":
/*!*********************************************************************!*\
  !*** ./includes/builders/gutenberg/src/blocks/ywsbs-plans/index.js ***!
  \*********************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _common__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../common */ "./includes/builders/gutenberg/src/common.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./edit */ "./includes/builders/gutenberg/src/blocks/ywsbs-plans/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./save */ "./includes/builders/gutenberg/src/blocks/ywsbs-plans/save.js");


function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }






var blockConfig = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Subscription Plans', 'yith-woocommerce-subscription'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add subscription table price', 'yith-woocommerce-subscription'),
  icon: _common__WEBPACK_IMPORTED_MODULE_3__["yith_icon"],
  category: 'yith-blocks',
  attributes: _common__WEBPACK_IMPORTED_MODULE_3__["attributesPlans"],
  example: {
    attributes: {
      preview: true
    }
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_4__["default"],
  save: _save__WEBPACK_IMPORTED_MODULE_5__["default"]
};
Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__["registerBlockType"])('yith/ywsbs-plans', _objectSpread({}, blockConfig));

/***/ }),

/***/ "./includes/builders/gutenberg/src/blocks/ywsbs-plans/save.js":
/*!********************************************************************!*\
  !*** ./includes/builders/gutenberg/src/blocks/ywsbs-plans/save.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return save; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

function save(_ref) {
  var attributes = _ref.attributes;
  var wrapperClass = ' ywsbs-plans ' + ' plans-' + attributes.plans;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: wrapperClass
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["InnerBlocks"].Content, null));
}

/***/ }),

/***/ "./includes/builders/gutenberg/src/blocks/ywsbs-price/edit.js":
/*!********************************************************************!*\
  !*** ./includes/builders/gutenberg/src/blocks/ywsbs-price/edit.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return edit; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);




function edit(props) {
  var attributes = props.attributes,
      setAttributes = props.setAttributes,
      className = props.className;
  var price = attributes.price,
      priceFontSize = attributes.priceFontSize,
      textColor = attributes.textColor;
  var recurringBillingPeriod = attributes.recurringBillingPeriod,
      billingPeriodFontSize = attributes.billingPeriodFontSize,
      billingPeriodPosition = attributes.billingPeriodPosition;
  var feeText = attributes.feeText,
      feeFontSize = attributes.feeFontSize,
      feeShow = attributes.feeShow;
  var trialText = attributes.trialText,
      trialFontSize = attributes.trialFontSize,
      trialShow = attributes.trialShow;
  var fontSizes = [{
    name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Small'),
    slug: 'small',
    size: 11
  }, {
    name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Medium'),
    slug: 'small',
    size: 13
  }, {
    name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Big'),
    slug: 'big',
    size: 40
  }];
  var billingPeriodClassName = "ywsbs-plan__price-billing " + billingPeriodPosition;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Price settings', 'yith-woocommerce-subscription')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["FontSizePicker"], {
    fontSizes: fontSizes,
    value: priceFontSize || 40,
    fallbackFontSize: 40,
    withSlider: true,
    onChange: function onChange(newFontSize) {
      setAttributes({
        priceFontSize: newFontSize
      });
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Billing period settings', 'yith-woocommerce-subscription')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["FontSizePicker"], {
    fontSizes: fontSizes,
    value: billingPeriodFontSize || 11,
    fallbackFontSize: 13,
    withSlider: true,
    onChange: function onChange(newFontSize) {
      setAttributes({
        billingPeriodFontSize: newFontSize
      });
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Fee settings', 'yith-woocommerce-subscription')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Show Fee Text?', 'yith-woocommerce-subscription'),
    help: feeShow ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('The fee wil be displayed', 'yith-woocommerce-subscription') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('There is no fee', 'yith-woocommerce-subscription'),
    checked: feeShow,
    onChange: function onChange(value) {
      return setAttributes({
        feeShow: value
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["FontSizePicker"], {
    fontSizes: fontSizes,
    value: feeFontSize || 13,
    fallbackFontSize: 13,
    withSlider: true,
    onChange: function onChange(newFontSize) {
      setAttributes({
        feeFontSize: newFontSize
      });
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Trial settings', 'yith-woocommerce-subscription')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Show Trial Text?', 'yith-woocommerce-subscription'),
    help: trialShow ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('The trial text wil be displayed', 'yith-woocommerce-subscription') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('There is no trial', 'yith-woocommerce-subscription'),
    checked: trialShow,
    onChange: function onChange(value) {
      return setAttributes({
        trialShow: value
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["FontSizePicker"], {
    fontSizes: fontSizes,
    value: trialFontSize || 13,
    fallbackFontSize: 13,
    withSlider: true,
    onChange: function onChange(newFontSize) {
      setAttributes({
        trialFontSize: newFontSize
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: className,
    style: {
      color: textColor
    }
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "ywsbs-price__content"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__["RichText"], {
    className: "ywsbs-plan__price",
    tagName: "span",
    onChange: function onChange(value) {
      return setAttributes({
        price: value
      });
    },
    value: price,
    style: {
      fontSize: priceFontSize
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__["RichText"], {
    className: "ywsbs-plan__price-billing",
    tagName: "span",
    onChange: function onChange(value) {
      return setAttributes({
        recurringBillingPeriod: value
      });
    },
    value: recurringBillingPeriod,
    style: {
      fontSize: billingPeriodFontSize + 'px'
    }
  })), feeShow && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__["RichText"], {
    className: "ywsbs-plan__fee",
    tagName: "div",
    onChange: function onChange(value) {
      return setAttributes({
        feeText: value
      });
    },
    value: feeText,
    style: {
      fontSize: feeFontSize
    }
  }), trialShow && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__["RichText"], {
    className: "ywsbs-plan__trial",
    tagName: "div",
    onChange: function onChange(value) {
      return setAttributes({
        trialText: value
      });
    },
    value: trialText,
    style: {
      fontSize: trialFontSize
    }
  })));
}

/***/ }),

/***/ "./includes/builders/gutenberg/src/blocks/ywsbs-price/index.js":
/*!*********************************************************************!*\
  !*** ./includes/builders/gutenberg/src/blocks/ywsbs-price/index.js ***!
  \*********************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _common__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../common */ "./includes/builders/gutenberg/src/common.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./edit */ "./includes/builders/gutenberg/src/blocks/ywsbs-price/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./save */ "./includes/builders/gutenberg/src/blocks/ywsbs-price/save.js");


function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }






var blockConfig = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Subscription Price', 'yith-woocommerce-subscription'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add subscription price inside the Subscription plan', 'yith-woocommerce-subscription'),
  icon: _common__WEBPACK_IMPORTED_MODULE_3__["yith_icon"],
  category: 'yith-blocks',
  parent: ['yith/ywsbs-plan'],
  styles: [{
    name: '',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Billing period inline', 'yith-woocommerce-subscription')
  }, {
    name: 'on-top',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Billing period on top', 'yith-woocommerce-subscription')
  }, {
    name: 'on-bottom',
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Billing period on bottom', 'yith-woocommerce-subscription')
  }],
  attributes: _common__WEBPACK_IMPORTED_MODULE_3__["attributesPrice"],
  edit: _edit__WEBPACK_IMPORTED_MODULE_4__["default"],
  save: _save__WEBPACK_IMPORTED_MODULE_5__["default"],
  "supports": {
    "__experimentalColor": true,
    "__experimentalLineHeight": true,
    "__experimentalFontSize": true
  }
};
Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__["registerBlockType"])('yith/ywsbs-price', _objectSpread({}, blockConfig));

/***/ }),

/***/ "./includes/builders/gutenberg/src/blocks/ywsbs-price/save.js":
/*!********************************************************************!*\
  !*** ./includes/builders/gutenberg/src/blocks/ywsbs-price/save.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return save; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

function save(_ref) {
  var attributes = _ref.attributes;
  var price = attributes.price,
      priceFontSize = attributes.priceFontSize;
  var recurringBillingPeriod = attributes.recurringBillingPeriod,
      billingPeriodFontSize = attributes.billingPeriodFontSize,
      billingPeriodPosition = attributes.billingPeriodPosition;
  var feeText = attributes.feeText,
      feeFontSize = attributes.feeFontSize,
      feeShow = attributes.feeShow;
  var trialText = attributes.trialText,
      trialFontSize = attributes.trialFontSize,
      trialShow = attributes.trialShow;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "ywsbs-price"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: "ywsbs-price__content"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["RichText"].Content, {
    className: "ywsbs-plan__price",
    tagName: "span",
    value: price,
    style: {
      fontSize: priceFontSize + 'px'
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["RichText"].Content, {
    className: "ywsbs-plan__price-billing",
    tagName: "span",
    value: recurringBillingPeriod,
    style: {
      fontSize: billingPeriodFontSize + 'px'
    }
  })), feeShow && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["RichText"].Content, {
    className: "ywsbs-plan__fee",
    tagName: "div",
    value: feeText,
    style: {
      fontSize: feeFontSize + 'px'
    }
  }), trialShow && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["RichText"].Content, {
    className: "ywsbs-plan__trial",
    tagName: "div",
    value: trialText,
    style: {
      fontSize: trialFontSize + 'px'
    }
  })));
}

/***/ }),

/***/ "./includes/builders/gutenberg/src/common.js":
/*!***************************************************!*\
  !*** ./includes/builders/gutenberg/src/common.js ***!
  \***************************************************/
/*! exports provided: yith_icon, attributesPlans, attributesPlan, attributesPrice */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "yith_icon", function() { return yith_icon; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "attributesPlans", function() { return attributesPlans; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "attributesPlan", function() { return attributesPlan; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "attributesPrice", function() { return attributesPrice; });
var el = wp.element.createElement;
var yith_icon = el('svg', {
  width: 22,
  height: 22
}, el('path', {
  d: "M 18.24 7.628 C 17.291 8.284 16.076 8.971 14.587 9.688 C 15.344 7.186 15.765 4.851 15.849 2.684 C 15.912 0.939 15.133 0.045 13.514 0.003 C 11.558 -0.06 10.275 1.033 9.665 3.284 C 10.007 3.137 10.359 3.063 10.723 3.063 C 11.021 3.063 11.267 3.184 11.459 3.426 C 11.651 3.668 11.736 3.947 11.715 4.262 C 11.695 5.082 11.276 5.961 10.46 6.896 C 9.644 7.833 8.918 8.3 8.282 8.3 C 7.837 8.3 7.625 7.922 7.646 7.165 C 7.667 6.765 7.804 5.955 8.056 4.735 C 8.287 3.579 8.403 2.801 8.403 2.401 C 8.403 1.707 8.224 1.144 7.867 0.713 C 7.509 0.282 6.994 0.098 6.321 0.161 C 5.858 0.203 5.175 0.624 4.27 1.422 C 3.596 2.035 2.923 2.644 2.25 3.254 L 2.976 4.106 C 3.564 3.664 3.922 3.443 4.048 3.443 C 4.448 3.443 4.637 3.717 4.617 4.263 C 4.617 4.306 4.427 4.968 4.049 6.251 C 3.671 7.534 3.471 8.491 3.449 9.122 C 3.407 9.985 3.565 10.647 3.924 11.109 C 4.367 11.677 5.106 11.919 6.142 11.835 C 7.366 11.751 8.591 11.298 9.816 10.479 C 10.323 10.142 10.808 9.753 11.273 9.311 C 11.105 10.153 10.905 10.868 10.673 11.457 C 8.402 12.487 6.762 13.37 5.752 14.107 C 4.321 15.137 3.554 16.241 3.449 17.419 C 3.259 19.459 4.29 20.479 6.541 20.479 C 8.055 20.479 9.517 19.554 10.926 17.703 C 12.125 16.126 13.166 14.022 14.049 11.394 C 15.578 10.635 16.87 9.892 17.928 9.164 C 17.894 9.409 18.319 7.308 18.24 7.628 Z  M 7.393 16.095 C 7.056 16.095 6.898 15.947 6.919 15.653 C 6.961 15.106 7.908 14.38 9.759 13.476 C 8.791 15.221 8.002 16.095 7.393 16.095 Z"
}));
var attributesPlans = {
  plans: {
    type: 'integer',
    default: 3
  },
  planTemplate: {
    type: 'array',
    default: [['yith/ywsbs-plan'], ['yith/ywsbs-plan'], ['yith/ywsbs-plan']]
  },
  preview: {
    type: 'boolean',
    default: false
  }
};
var attributesPlan = {
  textColor: {
    type: 'text',
    default: '#000'
  },
  title: {
    type: 'text',
    default: 'Title'
  },
  titleColor: {
    type: 'text',
    default: '#000'
  },
  titleAlign: {
    type: 'text',
    default: 'center'
  },
  titleBackgroundColor: {
    type: 'text',
    default: '#fff'
  },
  titleBackgroundColorTransparent: {
    type: 'boolean',
    default: false
  },
  titleFontSize: {
    type: 'number',
    default: '40'
  },
  backgroundColor: {
    type: 'text',
    default: '#fff'
  },
  borderColor: {
    type: 'text',
    default: '#dedede'
  },
  subtitleFontSize: {
    type: 'number',
    default: '40'
  },
  subtitleColor: {
    type: 'text',
    default: '#000'
  },
  showSubtitleSeparator: {
    type: 'boolean',
    default: false
  },
  subtitleLabel: {
    type: 'text',
    default: ''
  },
  animation: {
    type: 'text',
    default: ''
  },
  borderRadius: {
    type: 'integer',
    default: 10
  },
  showList: {
    type: 'boolean',
    default: false
  },
  showImage: {
    type: 'boolean',
    default: false
  },
  shadowColor: {
    type: 'text',
    default: '#d0d0d0'
  },
  shadowH: {
    type: 'number',
    default: 1
  },
  shadowV: {
    type: 'number',
    default: 1
  },
  shadowBlur: {
    type: 'number',
    default: 12
  },
  shadowSpread: {
    type: 'number',
    default: 0
  },
  id: {
    type: "number"
  },
  alt: {
    type: "string",
    source: "attribute",
    selector: "img",
    attribute: "alt",
    default: ""
  },
  url: {
    type: "string",
    source: "attribute",
    selector: "img",
    attribute: "src"
  }
};
var attributesPrice = {
  price: {
    type: 'text',
    default: '$19.90'
  },
  priceFontSize: {
    type: 'number',
    default: '40'
  },
  recurringBillingPeriod: {
    type: 'text',
    default: '/ Month'
  },
  billingPeriodFontSize: {
    type: 'number',
    default: '11'
  },
  billingPeriodPosition: {
    type: 'text',
    default: 'on-top'
  },
  feeText: {
    type: 'text',
    default: '+ a signup fee of 5,00$'
  },
  feeShow: {
    type: 'boolean',
    default: true
  },
  feeFontSize: {
    type: 'number',
    default: '13'
  },
  trialText: {
    type: 'text',
    default: 'Try it for 1 week free!'
  },
  trialShow: {
    type: 'boolean',
    default: true
  },
  trialFontSize: {
    type: 'number',
    default: '13'
  }
};


/***/ }),

/***/ "./includes/builders/gutenberg/src/components/title-plan.js":
/*!******************************************************************!*\
  !*** ./includes/builders/gutenberg/src/components/title-plan.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);







function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, result); }; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }





var TitlePlan = /*#__PURE__*/function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default()(TitlePlan, _Component);

  var _super = _createSuper(TitlePlan);

  function TitlePlan() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, TitlePlan);

    return _super.apply(this, arguments);
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(TitlePlan, [{
    key: "render",
    value: function render() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          updateAttribute = _this$props.updateAttribute,
          subtitleLabel = _this$props.subtitleLabel;
      var title = attributes.title,
          titleColor = attributes.titleColor,
          titleAlign = attributes.titleAlign,
          titleBackgroundColor = attributes.titleBackgroundColor,
          titleFontSize = attributes.titleFontSize,
          titleBackgroundColorTransparent = attributes.titleBackgroundColorTransparent;
      var subtitleFontSize = attributes.subtitleFontSize,
          showSubtitleSeparator = attributes.showSubtitleSeparator,
          subtitleColor = attributes.subtitleColor;
      var subtitleClass = 'subtitlePlan' + (showSubtitleSeparator ? ' with-separator' : '');
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("div", {
        className: "plan-title",
        style: titleBackgroundColorTransparent ? {
          background: 'transparent'
        } : {
          background: titleBackgroundColor
        }
      }, subtitleLabel !== '' ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("style", null, ".wp-block-yith-ywsbs-plan .subtitlePlan.with-separator:after { border-color: ".concat(subtitleColor, " }")) : '', subtitleLabel !== '' ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("div", {
        className: subtitleClass,
        style: {
          color: subtitleColor,
          fontSize: subtitleFontSize
        }
      }, subtitleLabel) : '', Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["RichText"], {
        className: "ywsbs-plan__title",
        tagName: "h2",
        onChange: function onChange(value) {
          return updateAttribute(value, 'title');
        },
        value: title,
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])("Title Plan", "mytheme-blocks"),
        formatingControls: [],
        style: {
          textAlign: titleAlign,
          color: titleColor,
          fontSize: titleFontSize
        }
      }));
    }
  }]);

  return TitlePlan;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (TitlePlan);

/***/ }),

/***/ "./includes/builders/gutenberg/src/index.js":
/*!**************************************************!*\
  !*** ./includes/builders/gutenberg/src/index.js ***!
  \**************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _blocks_ywsbs_plans__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./blocks/ywsbs-plans */ "./includes/builders/gutenberg/src/blocks/ywsbs-plans/index.js");
/* harmony import */ var _blocks_ywsbs_plan__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./blocks/ywsbs-plan */ "./includes/builders/gutenberg/src/blocks/ywsbs-plan/index.js");
/* harmony import */ var _blocks_ywsbs_price__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./blocks/ywsbs-price */ "./includes/builders/gutenberg/src/blocks/ywsbs-price/index.js");




/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

module.exports = _arrayLikeToArray;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return arrayLikeToArray(arr);
}

module.exports = _arrayWithoutHoles;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

module.exports = _assertThisInitialized;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

module.exports = _classCallCheck;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/createClass.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/createClass.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

module.exports = _createClass;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/defineProperty.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

module.exports = _defineProperty;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _getPrototypeOf(o) {
  module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

module.exports = _getPrototypeOf;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/inherits.js":
/*!*********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/inherits.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf */ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js");

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) setPrototypeOf(subClass, superClass);
}

module.exports = _inherits;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

module.exports = _iterableToArray;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

module.exports = _nonIterableSpread;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var _typeof = __webpack_require__(/*! ../helpers/typeof */ "./node_modules/@babel/runtime/helpers/typeof.js");

var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized */ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js");

function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return assertThisInitialized(self);
}

module.exports = _possibleConstructorReturn;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _setPrototypeOf(o, p) {
  module.exports = _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}

module.exports = _setPrototypeOf;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles */ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");

var iterableToArray = __webpack_require__(/*! ./iterableToArray */ "./node_modules/@babel/runtime/helpers/iterableToArray.js");

var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray */ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");

var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread */ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js");

function _toConsumableArray(arr) {
  return arrayWithoutHoles(arr) || iterableToArray(arr) || unsupportedIterableToArray(arr) || nonIterableSpread();
}

module.exports = _toConsumableArray;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/typeof.js":
/*!*******************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/typeof.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    module.exports = _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    module.exports = _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

module.exports = _typeof;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);
}

module.exports = _unsupportedIterableToArray;

/***/ }),

/***/ "@wordpress/block-editor":
/*!**********************************************!*\
  !*** external {"this":["wp","blockEditor"]} ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockEditor"]; }());

/***/ }),

/***/ "@wordpress/blocks":
/*!*****************************************!*\
  !*** external {"this":["wp","blocks"]} ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/compose":
/*!******************************************!*\
  !*** external {"this":["wp","compose"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ "@wordpress/data":
/*!***************************************!*\
  !*** external {"this":["wp","data"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ "lodash":
/*!**********************************!*\
  !*** external {"this":"lodash"} ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map