/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);
Object(function webpackMissingModule() { var e = new Error("Cannot find module './bootstrap'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }

document.addEventListener('DOMContentLoaded', function () {
  // Feature Cards Animation
  initializeFeatureCards();

  // Mountain Parallax Effect
  initializeMountainParallax();

  // Search Input Enhancement
  initializeSearchInput();

  // Button Animations
  initializeButtons();

  // Mobile Menu Toggle
  initializeMobileMenu();

  // Mountain Logo Animation
  initializeMountainLogo();

  // Add Custom Animations
  addCustomAnimations();

  // Trail Explorer Component
  var trailExplorerComponent = trailExplorer();
  trailExplorerComponent.init();
});
function initializeFeatureCards() {
  var cards = document.querySelectorAll('.feature-card');
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry, index) {
      if (entry.isIntersecting) {
        entry.target.style.animationDelay = "".concat(index * 0.15, "s");
        entry.target.style.animation = 'fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards';
      }
    });
  }, {
    threshold: 0.1
  });
  cards.forEach(function (card) {
    observer.observe(card);
    card.addEventListener('mouseenter', function () {
      this.style.animation = 'none';
      this.style.transform = 'translateY(-12px) scale(1.02) rotateX(5deg)';
      this.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.25)';
    });
    card.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0) scale(1) rotateX(0deg)';
      this.style.boxShadow = '';
    });
  });
}
function initializeMountainParallax() {
  var ticking = false;
  window.addEventListener('scroll', function () {
    if (!ticking) {
      window.requestAnimationFrame(function () {
        var scrolled = window.pageYOffset;
        var parallaxElements = document.querySelectorAll('.parallax-mountain');
        parallaxElements.forEach(function (el, index) {
          var speed = (index + 1) * 0.5;
          el.style.transform = "translateY(".concat(scrolled * speed, "px)");
        });
        ticking = false;
      });
      ticking = true;
    }
  });
}
function initializeSearchInput() {
  var searchInput = document.querySelector('.mountain-search');
  if (searchInput) {
    searchInput.addEventListener('focus', function () {
      this.style.transform = 'scale(1.02)';
      this.style.boxShadow = '0 12px 40px rgba(37, 99, 235, 0.15)';
    });
    searchInput.addEventListener('blur', function () {
      this.style.transform = 'scale(1)';
      this.style.boxShadow = '';
    });
  }
}
function initializeButtons() {
  var buttons = document.querySelectorAll('.btn-mountain');
  buttons.forEach(function (button) {
    button.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-3px) scale(1.05)';
      this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1)';
    });
    button.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0) scale(1)';
      this.style.boxShadow = '';
    });
  });
}
function initializeMobileMenu() {
  var menuButton = document.getElementById('mobile-menu-btn');
  var mobileMenu = document.getElementById('mobile-menu');
  if (menuButton && mobileMenu) {
    menuButton.addEventListener('click', function () {
      mobileMenu.classList.toggle('hidden');
      this.setAttribute('aria-expanded', this.getAttribute('aria-expanded') === 'false' ? 'true' : 'false');
    });
  }
}
function initializeMountainLogo() {
  var logo = document.querySelector('.mountain-logo');
  if (logo) {
    logo.addEventListener('mouseenter', function () {
      this.style.transform = 'scale(1.05)';
    });
    logo.addEventListener('mouseleave', function () {
      this.style.transform = 'scale(1)';
    });
  }
}
function addCustomAnimations() {
  var style = document.createElement('style');
  style.textContent = "\n        @keyframes mountainFloat {\n            0%, 100% { transform: translateY(0) rotateZ(0deg); }\n            25% { transform: translateY(-10px) rotateZ(0.5deg); }\n            50% { transform: translateY(-5px) rotateZ(0deg); }\n            75% { transform: translateY(-15px) rotateZ(-0.5deg); }\n        }\n        \n        @keyframes peakGlow {\n            0%, 100% { filter: drop-shadow(0 0 0 rgba(249, 115, 22, 0)); }\n            50% { filter: drop-shadow(0 0 20px rgba(249, 115, 22, 0.3)); }\n        }\n        \n        .mountain-scene svg {\n            animation: peakGlow 4s ease-in-out infinite;\n        }\n        \n        .feature-card {\n            backdrop-filter: blur(10px);\n            transition: transform 0.3s ease, box-shadow 0.3s ease;\n        }\n        \n        .hero-mountain-bg svg {\n            animation: mountainFloat 20s ease-in-out infinite;\n        }\n\n        .cloud {\n            animation: drift 20s linear infinite;\n        }\n    ";
  document.head.appendChild(style);
}

// Trail Explorer Component
function trailExplorer() {
  return {
    searchQuery: '',
    selectedLocation: '',
    filters: {
      difficulty: '',
      season: ''
    },
    sortBy: 'name',
    trails: [],
    filteredTrails: [],
    locations: [],
    loading: false,
    init: function init() {
      console.log('Initializing trailExplorer');
      this.loadLocations();
      this.fetchTrails();
    },
    loadLocations: function loadLocations() {
      var _this = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee() {
        var response, allLocations, _t;
        return _regenerator().w(function (_context) {
          while (1) switch (_context.p = _context.n) {
            case 0:
              _context.p = 0;
              _context.n = 1;
              return fetch('/api/locations');
            case 1:
              response = _context.v;
              _context.n = 2;
              return response.json();
            case 2:
              allLocations = _context.v;
              // Filter out any locations with blank or null names
              _this.locations = allLocations.filter(function (location) {
                return location.name && location.name.trim() !== '' && location.slug;
              });
              console.log('Loaded locations:', _this.locations); // Debug log
              _context.n = 4;
              break;
            case 3:
              _context.p = 3;
              _t = _context.v;
              console.error('Error loading locations:', _t);
              _this.locations = [];
            case 4:
              return _context.a(2);
          }
        }, _callee, null, [[0, 3]]);
      }))();
    },
    getValidLocations: function getValidLocations() {
      return this.locations.filter(function (location) {
        return location.name && location.name.trim() !== '' && location.slug;
      });
    },
    fetchTrails: function fetchTrails() {
      var _this2 = this;
      return _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2() {
        var url, params, response, _t2;
        return _regenerator().w(function (_context2) {
          while (1) switch (_context2.p = _context2.n) {
            case 0:
              _this2.loading = true;
              _context2.p = 1;
              url = '/api/trails';
              params = new URLSearchParams();
              if (_this2.selectedLocation) {
                params.append('location', _this2.selectedLocation);
              }
              if (_this2.filters.difficulty) {
                params.append('difficulty', _this2.filters.difficulty);
              }
              if (_this2.filters.season) {
                params.append('season', _this2.filters.season);
              }
              if (params.toString()) {
                url += '?' + params.toString();
              }
              _context2.n = 2;
              return fetch(url);
            case 2:
              response = _context2.v;
              _context2.n = 3;
              return response.json();
            case 3:
              _this2.trails = _context2.v;
              _this2.filteredTrails = _toConsumableArray(_this2.trails);
              _this2.sortTrails();
              _context2.n = 5;
              break;
            case 4:
              _context2.p = 4;
              _t2 = _context2.v;
              console.error('Error fetching trails:', _t2);
            case 5:
              _context2.p = 5;
              _this2.loading = false;
              return _context2.f(5);
            case 6:
              return _context2.a(2);
          }
        }, _callee2, null, [[1, 4, 5, 6]]);
      }))();
    },
    debounceSearch: function debounceSearch() {
      var _this3 = this;
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(function () {
        _this3.performSearch();
      }, 300);
    },
    performSearch: function performSearch() {
      if (!this.searchQuery.trim()) {
        this.filteredTrails = _toConsumableArray(this.trails);
      } else {
        var query = this.searchQuery.toLowerCase();
        this.filteredTrails = this.trails.filter(function (trail) {
          var _trail$location_name, _trail$organization;
          return trail.name.toLowerCase().includes(query) || trail.mountain_name.toLowerCase().includes(query) || ((_trail$location_name = trail.location_name) === null || _trail$location_name === void 0 ? void 0 : _trail$location_name.toLowerCase().includes(query)) || ((_trail$organization = trail.organization) === null || _trail$organization === void 0 ? void 0 : _trail$organization.toLowerCase().includes(query));
        });
      }
      this.sortTrails();
    },
    sortTrails: function sortTrails() {
      var _this4 = this;
      if (!this.sortBy) return;
      this.filteredTrails.sort(function (a, b) {
        switch (_this4.sortBy) {
          case 'name':
            return a.name.localeCompare(b.name);
          case 'difficulty':
            var difficultyOrder = {
              'beginner': 1,
              'intermediate': 2,
              'advanced': 3
            };
            return difficultyOrder[a.difficulty] - difficultyOrder[b.difficulty];
          case 'length':
            return parseFloat(a.length) - parseFloat(b.length);
          case 'rating':
            return (b.average_rating || 0) - (a.average_rating || 0);
          case 'price':
            return parseFloat(a.price) - parseFloat(b.price);
          default:
            return 0;
        }
      });
    },
    resetFilters: function resetFilters() {
      this.searchQuery = '';
      this.selectedLocation = '';
      this.filters.difficulty = '';
      this.filters.season = '';
      this.sortBy = 'name';
      this.fetchTrails();
    },
    viewFullTrail: function viewFullTrail(trail) {
      window.location.href = "/trails/".concat(trail.slug);
    },
    downloadTrail: function downloadTrail(trail) {
      // Implementation for downloading trail information
      console.log('Downloading trail:', trail.name);
      // You can implement actual download functionality here
      alert("Downloading trail information for ".concat(trail.name));
    },
    getTrailImage: function getTrailImage(trail) {
      // Fallback image if no primary image is available
      var fallbackImages = ['https://images.unsplash.com/photo-1551632811-561732d1e306?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1464822759844-d150baec0134?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop', 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=800&h=600&fit=crop'];

      // Use trail ID to consistently get the same image for the same trail
      var index = trail.id % fallbackImages.length;
      return fallbackImages[index];
    },
    getDifficultyBadgeClass: function getDifficultyBadgeClass(difficulty) {
      var classes = {
        'beginner': 'bg-green-500 text-white',
        'intermediate': 'bg-yellow-500 text-white',
        'advanced': 'bg-red-500 text-white'
      };
      return classes[difficulty] || 'bg-gray-500 text-white';
    },
    getDifficultyLabel: function getDifficultyLabel(difficulty) {
      var labels = {
        'beginner': 'Beginner',
        'intermediate': 'Intermediate',
        'advanced': 'Advanced'
      };
      return labels[difficulty] || difficulty;
    },
    formatPrice: function formatPrice(price) {
      return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
      }).format(price);
    }
  };
}

// Make it globally available
window.trailExplorer = trailExplorer;
/******/ })()
;