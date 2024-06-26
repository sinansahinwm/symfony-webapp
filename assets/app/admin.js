// ---- Imports ---- //

// Admin CSS
import './admin.css';

// Toastr
import * as toastr from 'toastr';
import './theme/libs/toastr/toastr.scss';

// CrudTable
import './theme/libs/datatables-bs5/datatables-bootstrap5';
import './theme/libs/datatables-bs5/datatables.bootstrap5.scss';
import './theme/libs/datatables-responsive-bs5/responsive.bootstrap5.scss';

// Spinkit
import './theme/libs/spinkit/spinkit.scss';

// Select2
import './theme/libs/select2/select2';
import './theme/libs/select2/select2.scss';

// Cleave.JS
import {Cleave} from "./theme/libs/cleavejs/cleave";

// Perfect Scrollbar
import PerfectScrollbar from "perfect-scrollbar";


const adminPagesGlobalInitializer = function () {

    const _initMaskedInputs = function () {
        const creditCardMask = document.querySelector('.credit-card-mask');
        const expiryDateMask = document.querySelector('.expiry-date-mask');
        const cvvMask = document.querySelector('.cvv-code-mask');

        // Credit Card
        if (creditCardMask) {
            new Cleave(creditCardMask, {
                creditCard: true,
                onCreditCardTypeChanged: function (type) {
                    // TODO : Update UI
                }
            });
        }

        // Expiry Date Mask
        if (expiryDateMask) {
            new Cleave(expiryDateMask, {
                date: true,
                delimiter: '/',
                datePattern: ['m', 'y']
            });
        }

        // CVV
        if (cvvMask) {
            new Cleave(cvvMask, {
                numeral: true,
                numeralPositiveOnly: true
            });
        }

    }
    const _initKeyboardSearch = function () {
        if ($('.search-toggler').length > 0) {
            $(document).on('keydown', function (event) {
                let ctrlKey = event.ctrlKey, slashKey = event.which === 85;
                if (ctrlKey && slashKey) {
                    window.location.replace($('.search-toggler').attr('href'));
                }
            });
        }
    }

    const _initSidebarIndicator = function () {
        const contentTitleEl = $('#contentTitle');
        if (contentTitleEl) {
            try {
                const menuItemID = "#menu_item_" + contentTitleEl.html()
                    .replaceAll(" ", '')
                    .replaceAll('\n', '')
                    .replaceAll('\t', '');
                const myMenuItem = $(menuItemID);
                if (myMenuItem) {
                    myMenuItem.addClass('active');
                    const parentSubMenu = myMenuItem.parent('.menu-sub');
                    if (parentSubMenu) {
                        const parentMenuItem = parentSubMenu.parent(".menu-item");
                        if (parentMenuItem) {
                            parentMenuItem.addClass("active");
                        }
                    }
                }
            } catch (err) {
                console.log("An error occured when initializing sidebar indicator." + err);
            }

        }
    }

    const _initSelect2 = function () {
        const langCode = $('html')[0].lang ?? 'en';
        const langFile = require('select2/src/js/select2/i18n/' + langCode + '.js');
        const select2Defaults = {
            allowClear: true,
            language: langFile
        };
        $('.select2').select2(select2Defaults);
    }

    const _initTooltips = function () {
        //$('[data-toggle="tooltip"]').tooltip();
    }

    const _initCardActions = function () {

        const configuratorElementList = [].slice.call(document.querySelectorAll('.card-table-configurator'));
        const closeElementList = [].slice.call(document.querySelectorAll('.card-close'));
        const expandElementList = [].slice.call(document.querySelectorAll('.card-expand'));
        const collapseElementList = [].slice.call(document.querySelectorAll('.card-collapsible'));

        // Card Action : [CONFIGURATOR]
        if (configuratorElementList) {
            // TODO : TABLE CONFIGURATOR console.log("CONFIG");
        }

        // Card Action : [CLOSE]
        if (closeElementList) {
            closeElementList.map(function (closeElement) {
                closeElement.addEventListener('click', event => {
                    event.preventDefault();
                    closeElement.closest('.card').classList.add('d-none');
                });
            });
        }

        // Card Action : [FULLSCREEN]
        if (expandElementList) {
            expandElementList.map(function (expandElement) {
                expandElement.addEventListener('click', event => {
                    event.preventDefault();
                    // Toggle class bx-fullscreen & bx-exit-fullscreen
                    Helpers._toggleClass(expandElement.firstElementChild, 'bx-fullscreen', 'bx-exit-fullscreen');

                    expandElement.closest('.card').classList.toggle('card-fullscreen');
                });
            });
        }

        // Card Action : [FULLSCREEN ESC]
        document.addEventListener('keyup', event => {
            event.preventDefault();
            //Esc button
            if (event.key === 'Escape') {
                const cardFullscreen = document.querySelector('.card-fullscreen');
                // Toggle class bx-fullscreen & bx-exit-fullscreen

                if (cardFullscreen) {
                    Helpers._toggleClass(
                        cardFullscreen.querySelector('.card-expand').firstChild,
                        'bx-fullscreen',
                        'bx-exit-fullscreen'
                    );
                    cardFullscreen.classList.toggle('card-fullscreen');
                }
            }
        });

        // Card Action : [COLLAPSE]
        if (collapseElementList) {
            collapseElementList.map(function (collapseElement) {
                collapseElement.addEventListener('click', event => {
                    event.preventDefault();
                    // Collapse the element
                    new bootstrap.Collapse(collapseElement.closest('.card').querySelector('.collapse'));
                    // Toggle collapsed class in `.card-header` element
                    collapseElement.closest('.card-header').classList.toggle('collapsed');
                    // Toggle class bx-chevron-down & bx-chevron-up
                    Helpers._toggleClass(collapseElement.firstElementChild, 'bx-chevron-down', 'bx-chevron-up');
                });
            });
        }


    }

    const _initCheckboxModalInitializer = function () {
        $('.showModalOnCheck').change(function () {
            if ($(this).is(':checked')) {
                const showModalId = $(this).attr('modalId');
                const myModal = new bootstrap.Modal($('#' + showModalId), {});
                myModal.show();
            }
        });
    }

    const _initPageNotifications = function () {
        $(".pageNotificationSuccess").each(function (index) {
            toastr["success"]($(this).val());
        });
        $(".pageNotificationError").each(function (index) {
            toastr["error"]($(this).val());
        });
    }

    const _initCrudTables = function () {


        $('.crudTableDecorator').each(function (index) {

            // Get Decorator Data
            const tableDataTarget = $(this).attr('data-target');
            const tableDataPath = $(this).attr('data-path');
            const tableDataFQCN = $(this).attr('data-fqcn');
            const tableOminesOptions = JSON.parse($(this).attr('data-options'));
            const tableLanguageOptions = JSON.parse($(this).attr('data-language'));

            // Extend Datatable Defaults
            $.fn.dataTable.defaults.method = tableOminesOptions.method;
            $.fn.dataTable.defaults.url = window.location.origin + tableDataPath + window.location.search;

            // Create Variables
            const defaultConfig = $.extend({}, $.fn.dataTable.defaults, tableOminesOptions);

            const ajaxUrl = typeof defaultConfig.url === 'function' ? defaultConfig.url(null) : defaultConfig.url;
            $.ajax(ajaxUrl, {
                method: defaultConfig.method,
                data: {
                    _dt: defaultConfig.name,
                    _init: true,
                    _fqcn: tableDataFQCN,
                }
            }).done(function (responseData) {

                const dtOpts = {

                    // Columns
                    columns: responseData.options.columns,

                    // Order
                    order: responseData.options.order,
                    orderCellsTop: responseData.options.orderCellsTop,
                    ordering: responseData.options.ordering,

                    // Paging
                    displayStart: responseData.options.displayStart,
                    pageLength: responseData.options.pageLength,
                    paging: responseData.options.paging,
                    pagingType: responseData.options.pagingType,
                    lengthChange: responseData.options.lengthChange,
                    lengthMenu: responseData.options.lengthMenu,

                    // Search
                    search: responseData.options.search,
                    searchDelay: responseData.options.searchDelay,
                    searching: responseData.options.searching,

                    // State Saving -> DISABLED IMMEDIATELY
                    stateSave: false,

                    // Header Fix
                    fixedHeader: false,

                    // Dom
                    // DEPRECED FOR NEW VERSION BROKES THE DOM
                    // dom: responseData.options.dom,

                    // Language
                    language: tableLanguageOptions,

                    // Processing ServerSide
                    processing: responseData.options.processing,
                    serverSide: responseData.options.serverSide,
                    ajax: function (request, drawCallback, settings) {
                        request._fqcn = tableDataFQCN;
                        if (responseData) {
                            responseData.draw = request.draw;
                            drawCallback(responseData);
                            responseData = null;
                        } else {
                            request._dt = defaultConfig.name;
                            $.ajax(typeof defaultConfig.url === 'function' ? defaultConfig.url(dt) : defaultConfig.url, {
                                method: defaultConfig.method,
                                data: request
                            }).done(function (data) {
                                drawCallback(data);
                            })
                        }
                    },

                };

                // Set Columns HTML
                $(tableDataTarget).html(responseData.template);

                // Check HeaderFixed
                let headerIsFixed = responseData.options.fixedHeader;

                // Add Redrawn Cells Function
                function initComponentsAfterTableReDraw() {
                    _initShowMoreSpan();
                    _initSelect2();
                    _initTooltips();
                }

                // Initialize Database
                const myTable = $(tableDataTarget).on('init.dt', function () {
                    initComponentsAfterTableReDraw();
                }).DataTable(dtOpts);

                // Fixed Header
                if (headerIsFixed) {
                    if (window.Helpers.isNavbarFixed()) {
                        const navHeight = $('#layout-navbar').outerHeight();
                        // new $.fn.dataTable.FixedHeader(myTable).headerOffset(navHeight);
                    } else {
                        // new $.fn.dataTable.FixedHeader(myTable);
                    }
                }

                // Remove Overlay
                $('#crudLoaderCardOverlay').fadeOut("slow", function () {
                    $(this).remove();
                });

                // Add Datatable Drawn Event
                myTable.on('draw.dt', function () {
                    initComponentsAfterTableReDraw();
                });

                // Add Datatable Error Event
                myTable.on('error.dt', function (e, settings, techNote, message) {
                    const tableFirstParent = myTable.parent();
                    tableFirstParent.html(message);
                });

            });


        });

    }

    const _initSymfonyToolbarBlock = function () {
        $('.sf-toolbar').remove();
    }

    const _initShowMoreSpan = function () {
        const staticOffCanvas = document.getElementById('showMoreSpanOffCanvas');
        $(".showMoreSpan").parent().on("click", function (index) {
            $('#showMoreSpanOffCanvasBody').html($(this).find('.showMoreSpan').attr('data-content'));
            const showMoreSpanOffCanvas = new bootstrap.Offcanvas(staticOffCanvas);
            showMoreSpanOffCanvas.show();
        });
    }

    const _initPerfectScrollbars = function () {

        const chatContactsBody = document.querySelector('.app-chat-contacts .sidebar-body');
        const chatHistoryBody = document.querySelector('.chat-history-body');

        // Init Chat Contacts Body
        if (chatContactsBody) {
            new PerfectScrollbar(chatContactsBody, {
                wheelPropagation: false,
                suppressScrollX: true
            });
        }

        // Init Chat History Body
        if (chatHistoryBody) {
            new PerfectScrollbar(chatHistoryBody, {
                wheelPropagation: false,
                suppressScrollX: true
            });
        }

    }

    return {
        init: function () {
            _initSidebarIndicator();
            _initKeyboardSearch();
            _initPageNotifications();
            _initCheckboxModalInitializer();
            _initCrudTables();
            _initCardActions();
            _initTooltips();
            _initSelect2();
            _initShowMoreSpan();
            _initPerfectScrollbars();
            _initMaskedInputs();
            // DEPRECED _initSymfonyToolbarBlock();
        }
    }

};

document.addEventListener("DOMContentLoaded", function () {
    adminPagesGlobalInitializer().init();
});
