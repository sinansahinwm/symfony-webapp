/**
 * Main
 */

'use strict';

import * as appHelpers from "./helpers";
window.Helpers = appHelpers.Helpers;

import * as appMenu from './menu';
window.Menu = appMenu.Menu;

// App Config
let config = {
    colors: {
        primary: '#5a8dee',
        secondary: '#69809a',
        success: '#39da8a',
        info: '#00cfdd',
        warning: '#fdac41',
        danger: '#ff5b5c',
        dark: '#495563',
        black: '#000',
        white: '#fff',
        cardColor: '#fff',
        bodyBg: '#f2f2f6',
        bodyColor: '#677788',
        headingColor: '#516377',
        textMuted: '#a8b1bb',
        borderColor: '#e9ecee'
    },
    colors_label: {
        primary: '#5a8dee29',
        secondary: '#69809a29',
        success: '#39da8a29',
        info: '#00cfdd29',
        warning: '#fdac4129',
        danger: '#ff5b5c29',
        dark: '#49556329'
    },
    colors_dark: {
        cardColor: '#283144',
        bodyBg: '#1c222f',
        bodyColor: '#a1b0cb',
        headingColor: '#d8deea',
        textMuted: '#8295ba',
        borderColor: '#36445d'
    },
    enableMenuLocalStorage: true,
    defaultContentLayout: 'wide',
};

let assetsPath = document.documentElement.getAttribute('data-assets-path'),
    templateName = document.documentElement.getAttribute('data-template')

let isRtl = window.Helpers.isRtl(),
    isDarkStyle = window.Helpers.isDarkStyle(),
    menu,
    animate,
    isHorizontalLayout = false;

if (document.getElementById('layout-menu')) {
    isHorizontalLayout = document.getElementById('layout-menu').classList.contains('menu-horizontal');
}

(function () {
    setTimeout(function () {
        window.Helpers.initCustomOptionCheck();
    }, 1000);

    // Initialize menu
    //-----------------

    let layoutMenuEl = document.querySelectorAll('#layout-menu');
    layoutMenuEl.forEach(function (element) {
        menu = new window.Menu.Menu(element, {
            orientation: isHorizontalLayout ? 'horizontal' : 'vertical',
            closeChildren: isHorizontalLayout ? true : false,
            // ? This option only works with Horizontal menu
            showDropdownOnHover: localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover') // If value(showDropdownOnHover) is set in local storage
                ? localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover') === 'true' // Use the local storage value
                : window.templateCustomizer !== undefined // If value is set in config.js
                    ? window.templateCustomizer.settings.defaultShowDropdownOnHover // Use the config.js value
                    : true // Use this if you are not using the config.js and want to set value directly from here
        });
        // Change parameter to true if you want scroll animation
        window.Helpers.scrollToActive((animate = false));
        window.Helpers.mainMenu = menu;
    });

    // Initialize menu togglers and bind click on each
    let menuToggler = document.querySelectorAll('.layout-menu-toggle');
    menuToggler.forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            window.Helpers.toggleCollapsed();
            // Enable menu state with local storage support if enableMenuLocalStorage = true from config.js
            if (config.enableMenuLocalStorage && !window.Helpers.isSmallScreen()) {
                try {
                    localStorage.setItem(
                        'templateCustomizer-' + templateName + '--LayoutCollapsed',
                        String(window.Helpers.isCollapsed())
                    );
                    // Update customizer checkbox state on click of menu toggler
                    let layoutCollapsedCustomizerOptions = document.querySelector('.template-customizer-layouts-options');
                    if (layoutCollapsedCustomizerOptions) {
                        let layoutCollapsedVal = window.Helpers.isCollapsed() ? 'collapsed' : 'expanded';
                        layoutCollapsedCustomizerOptions.querySelector(`input[value="${layoutCollapsedVal}"]`).click();
                    }
                } catch (e) {}
            }
        });
    });

    // Menu swipe gesture

    // Detect swipe gesture on the target element and call swipe In
    window.Helpers.swipeIn('.drag-target', function (e) {
        window.Helpers.setCollapsed(false);
    });

    // Detect swipe gesture on the target element and call swipe Out
    window.Helpers.swipeOut('#layout-menu', function (e) {
        if (window.Helpers.isSmallScreen()) window.Helpers.setCollapsed(true);
    });

    // Display in main menu when menu scrolls
    let menuInnerContainer = document.getElementsByClassName('menu-inner'),
        menuInnerShadow = document.getElementsByClassName('menu-inner-shadow')[0];
    if (menuInnerContainer.length > 0 && menuInnerShadow) {
        menuInnerContainer[0].addEventListener('ps-scroll-y', function () {
            if (this.querySelector('.ps__thumb-y').offsetTop) {
                menuInnerShadow.style.display = 'block';
            } else {
                menuInnerShadow.style.display = 'none';
            }
        });
    }

    // Update light/dark image based on current style
    function switchImage(style) {
        if (style === 'system') {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                style = 'dark';
            } else {
                style = 'light';
            }
        }
        const switchImagesList = [].slice.call(document.querySelectorAll('[data-app-' + style + '-img]'));
        switchImagesList.map(function (imageEl) {
            const setImage = imageEl.getAttribute('data-app-' + style + '-img');
            imageEl.src = assetsPath + 'img/' + setImage; // Using window.assetsPath to get the exact relative path
        });
    }

    // Navbar Scroll class
    //---------------------
    function scrollTopFn() {
        if (document.getElementById('layout-navbar')) {
            if (document.body.scrollTop > 10 || document.documentElement.scrollTop > 10) {
                document.getElementById('layout-navbar').classList.add('navbar-elevated');
            } else {
                document.getElementById('layout-navbar').classList.remove('navbar-elevated');
            }
        }
    }
    window.onscroll = function () {
        scrollTopFn();
    };

    //Style Switcher (Light/Dark/System Mode)
    let styleSwitcher = document.querySelector('.dropdown-style-switcher');

    // Get style from local storage or use 'system' as default
    let storedStyle =
        localStorage.getItem('templateCustomizer-' + templateName + '--Style') || //if no template style then use Customizer style
        (window.templateCustomizer?.settings?.defaultStyle ?? 'light'); //!if there is no Customizer then use default style as light

    // Set style on click of style switcher item if template customizer is enabled
    if (window.templateCustomizer && styleSwitcher) {
        let styleSwitcherItems = [].slice.call(styleSwitcher.children[1].querySelectorAll('.dropdown-item'));
        styleSwitcherItems.forEach(function (item) {
            item.addEventListener('click', function () {
                let currentStyle = this.getAttribute('data-theme');
                if (currentStyle === 'light') {
                    window.templateCustomizer.setStyle('light');
                } else if (currentStyle === 'dark') {
                    window.templateCustomizer.setStyle('dark');
                } else {
                    window.templateCustomizer.setStyle('system');
                }
            });
        });

        // Update style switcher icon based on the stored style

        const styleSwitcherIcon = styleSwitcher.querySelector('i');

        if (storedStyle === 'light') {
            styleSwitcherIcon.classList.add('bx-sun');
            new bootstrap.Tooltip(styleSwitcherIcon, {
                title: 'Light Mode',
                fallbackPlacements: ['bottom']
            });
        } else if (storedStyle === 'dark') {
            styleSwitcherIcon.classList.add('bx-moon');
            new bootstrap.Tooltip(styleSwitcherIcon, {
                title: 'Dark Mode',
                fallbackPlacements: ['bottom']
            });
        } else {
            styleSwitcherIcon.classList.add('bx-desktop');
            new bootstrap.Tooltip(styleSwitcherIcon, {
                title: 'System Mode',
                fallbackPlacements: ['bottom']
            });
        }
    }

    // Run switchImage function based on the stored style
    switchImage(storedStyle);

    // Internationalization (Language Dropdown)
    // ---------------------------------------

    if (typeof i18next !== 'undefined' && typeof i18NextHttpBackend !== 'undefined') {
        i18next
            .use(i18NextHttpBackend)
            .init({
                lng: window.templateCustomizer ? window.templateCustomizer.settings.lang : 'en',
                debug: false,
                fallbackLng: 'en',
                backend: {
                    loadPath: assetsPath + 'json/locales/{{lng}}.json'
                },
                returnObjects: true
            })
            .then(function (t) {
                localize();
            });
    }

    let languageDropdown = document.getElementsByClassName('dropdown-language');

    if (languageDropdown.length) {
        let dropdownItems = languageDropdown[0].querySelectorAll('.dropdown-item');

        for (let i = 0; i < dropdownItems.length; i++) {
            dropdownItems[i].addEventListener('click', function () {
                let currentLanguage = this.getAttribute('data-language');
                let textDirection = this.getAttribute('data-text-direction');

                for (let sibling of this.parentNode.children) {
                    var siblingEle = sibling.parentElement.parentNode.firstChild;

                    // Loop through each sibling and push to the array
                    while (siblingEle) {
                        if (siblingEle.nodeType === 1 && siblingEle !== siblingEle.parentElement) {
                            siblingEle.querySelector('.dropdown-item').classList.remove('active');
                        }
                        siblingEle = siblingEle.nextSibling;
                    }
                }
                this.classList.add('active');

                i18next.changeLanguage(currentLanguage, (err, t) => {
                    window.templateCustomizer ? window.templateCustomizer.setLang(currentLanguage) : '';
                    directionChange(textDirection);
                    if (err) return console.log('something went wrong loading', err);
                    localize();
                });
            });
        }
        function directionChange(textDirection) {
            if (textDirection === 'rtl') {
                if (localStorage.getItem('templateCustomizer-' + templateName + '--Rtl') !== 'true')
                    window.templateCustomizer ? window.templateCustomizer.setRtl(true) : '';
            } else {
                if (localStorage.getItem('templateCustomizer-' + templateName + '--Rtl') === 'true')
                    window.templateCustomizer ? window.templateCustomizer.setRtl(false) : '';
            }
        }
    }

    function localize() {
        let i18nList = document.querySelectorAll('[data-i18n]');
        // Set the current language in dd
        let currentLanguageEle = document.querySelector('.dropdown-item[data-language="' + i18next.language + '"]');

        if (currentLanguageEle) {
            currentLanguageEle.click();
        }

        i18nList.forEach(function (item) {
            item.innerHTML = i18next.t(item.dataset.i18n);
        });
    }

    // Notification
    // ------------
    const notificationMarkAsReadAll = document.querySelector('.dropdown-notifications-all');
    const notificationMarkAsReadList = document.querySelectorAll('.dropdown-notifications-read');

    // Notification: Mark as all as read
    if (notificationMarkAsReadAll) {
        notificationMarkAsReadAll.addEventListener('click', event => {
            notificationMarkAsReadList.forEach(item => {
                item.closest('.dropdown-notifications-item').classList.add('marked-as-read');
            });
        });
    }
    // Notification: Mark as read/unread onclick of dot
    if (notificationMarkAsReadList) {
        notificationMarkAsReadList.forEach(item => {
            item.addEventListener('click', event => {
                item.closest('.dropdown-notifications-item').classList.toggle('marked-as-read');
            });
        });
    }

    // Notification: Mark as read/unread onclick of dot
    const notificationArchiveMessageList = document.querySelectorAll('.dropdown-notifications-archive');
    notificationArchiveMessageList.forEach(item => {
        item.addEventListener('click', event => {
            item.closest('.dropdown-notifications-item').remove();
        });
    });

    // Init helpers & misc
    // --------------------

    // Init BS Tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Accordion active class
    const accordionActiveFunction = function (e) {
        if (e.type == 'show.bs.collapse' || e.type == 'show.bs.collapse') {
            e.target.closest('.accordion-item').classList.add('active');
        } else {
            e.target.closest('.accordion-item').classList.remove('active');
        }
    };

    const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
    const accordionList = accordionTriggerList.map(function (accordionTriggerEl) {
        accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
        accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
    });

    // If layout is RTL add .dropdown-menu-end class to .dropdown-menu
    // if (isRtl) {
    //   Helpers._addClass('dropdown-menu-end', document.querySelectorAll('#layout-navbar .dropdown-menu'));
    // }

    // Auto update layout based on screen size
    window.Helpers.setAutoUpdate(true);

    // Toggle Password Visibility
    window.Helpers.initPasswordToggle();

    // Speech To Text
    window.Helpers.initSpeechToText();

    // Init PerfectScrollbar in Navbar Dropdown (i.e notification)
    window.Helpers.initNavbarDropdownScrollbar();

    let horizontalMenuTemplate = document.querySelector("[data-template^='horizontal-menu']");
    if (horizontalMenuTemplate) {
        // if screen size is small then set navbar fixed
        if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
            window.Helpers.setNavbarFixed('fixed');
        } else {
            window.Helpers.setNavbarFixed('');
        }
    }

    // On window resize listener
    // -------------------------
    window.addEventListener(
        'resize',
        function (event) {
            // Hide open search input and set value blank
            if (window.innerWidth >= window.Helpers.LAYOUT_BREAKPOINT) {
                if (document.querySelector('.search-input-wrapper')) {
                    document.querySelector('.search-input-wrapper').classList.add('d-none');
                    document.querySelector('.search-input').value = '';
                }
            }
            // Horizontal Layout : Update menu based on window size
            if (horizontalMenuTemplate) {
                // if screen size is small then set navbar fixed
                if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
                    window.Helpers.setNavbarFixed('fixed');
                } else {
                    window.Helpers.setNavbarFixed('');
                }
                setTimeout(function () {
                    if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
                        if (document.getElementById('layout-menu')) {
                            if (document.getElementById('layout-menu').classList.contains('menu-horizontal')) {
                                menu.switchMenu('vertical');
                            }
                        }
                    } else {
                        if (document.getElementById('layout-menu')) {
                            if (document.getElementById('layout-menu').classList.contains('menu-vertical')) {
                                menu.switchMenu('horizontal');
                            }
                        }
                    }
                }, 100);
            }
        },
        true
    );

    // Manage menu expanded/collapsed with templateCustomizer & local storage
    //------------------------------------------------------------------

    // If current layout is horizontal OR current window screen is small (overlay menu) than return from here
    if (isHorizontalLayout || window.Helpers.isSmallScreen()) {
        return;
    }

    // If current layout is vertical and current window screen is > small

    // Auto update menu collapsed/expanded based on the themeConfig
    if (typeof TemplateCustomizer !== 'undefined') {
        if (window.templateCustomizer.settings.defaultMenuCollapsed) {
            window.Helpers.setCollapsed(true, false);
        } else {
            window.Helpers.setCollapsed(false, false);
        }
    }

    // Manage menu expanded/collapsed state with local storage support If enableMenuLocalStorage = true in config.js
    if (typeof config !== 'undefined') {
        if (config.enableMenuLocalStorage) {
            try {
                if (localStorage.getItem('templateCustomizer-' + templateName + '--LayoutCollapsed') !== null)
                    window.Helpers.setCollapsed(
                        localStorage.getItem('templateCustomizer-' + templateName + '--LayoutCollapsed') === 'true',
                        false
                    );
            } catch (e) {}
        }
    }
})();

// ! Removed following code if you do't wish to use jQuery. Remember that navbar search functionality will stop working on removal.
if (typeof $ !== 'undefined') {
    $(function () {
        // ! TODO: Required to load after DOM is ready, did this now with jQuery ready.
        window.Helpers.initSidebarToggle();
    });
}