/*
|--------------------------------------------------------------------------
| CISALADA GO - GLOBAL ANIMATION
|--------------------------------------------------------------------------
| File:
| assets/js/animation.js
|
| Fungsi:
| 1. Scroll reveal otomatis
| 2. Card muncul bertahap
| 3. Section muncul saat masuk viewport
| 4. Efek gambar
| 5. Tidak mengubah sistem PHP / database / CRUD
|--------------------------------------------------------------------------
*/

(function () {

    "use strict";


    // ==========================================================
    // CEK PENGGUNA MEMATIKAN ANIMASI
    // ==========================================================

    const reducedMotion =
        window.matchMedia(
            "(prefers-reduced-motion: reduce)"
        ).matches;


    if (
        reducedMotion
    ) {

        return;

    }



    // ==========================================================
    // ELEMENT YANG TIDAK BOLEH DIBERI ANIMASI
    // ==========================================================

    const excludedSelectors = [
        ".navbar",
        ".cisalada-navbar",
        ".page-loader",
        "#cisalada-page-loader",
        "#cisalada-full-loader",
        "#cisalada-thin-loader",
        "footer",
        ".leaflet-container",
        ".map-container",
        ".gm-style"
    ];



    // ==========================================================
    // CEK APAKAH ELEMENT DIKECUALIKAN
    // ==========================================================

    function isExcluded(
        element
    ) {

        if (
            !element
        ) {

            return true;

        }


        for (
            const selector of excludedSelectors
        ) {

            try {

                if (
                    element.matches(
                        selector
                    )
                    ||
                    element.closest(
                        selector
                    )
                ) {

                    return true;

                }

            } catch (
                error
            ) {

                // Abaikan selector yang bermasalah.

            }

        }


        return false;

    }



    // ==========================================================
    // TAMBAHKAN CLASS REVEAL
    // ==========================================================

    function prepareElements()
    {

        // ======================================================
        // SECTION
        // ======================================================

        const sections =
            document.querySelectorAll(
                "section"
            );


        sections.forEach(
            function (
                section
            ) {

                if (
                    isExcluded(
                        section
                    )
                ) {

                    return;

                }


                if (
                    section.classList.contains(
                        "reveal"
                    )
                    ||
                    section.classList.contains(
                        "reveal-left"
                    )
                    ||
                    section.classList.contains(
                        "reveal-right"
                    )
                    ||
                    section.classList.contains(
                        "reveal-scale"
                    )
                ) {

                    return;

                }


                section.classList.add(
                    "reveal"
                );

            }
        );



        // ======================================================
        // CARD
        // ======================================================

        const cards =
            document.querySelectorAll(
                ".card"
            );


        cards.forEach(
            function (
                card
            ) {

                if (
                    isExcluded(
                        card
                    )
                ) {

                    return;

                }


                if (
                    card.closest(
                        "nav"
                    )
                ) {

                    return;

                }


                if (
                    card.classList.contains(
                        "reveal"
                    )
                    ||
                    card.classList.contains(
                        "reveal-stagger"
                    )
                    ||
                    card.classList.contains(
                        "reveal-scale"
                    )
                ) {

                    return;

                }


                card.classList.add(
                    "reveal-stagger"
                );

            }
        );



        // ======================================================
        // INFO / QUICK CARD
        // ======================================================

        const specialCards =
            document.querySelectorAll(
                ".info-card, .quick-card, .stat-card"
            );


        specialCards.forEach(
            function (
                card
            ) {

                if (
                    isExcluded(
                        card
                    )
                ) {

                    return;

                }


                card.classList.add(
                    "reveal-stagger"
                );

            }
        );



        // ======================================================
        // JUDUL SECTION
        // ======================================================

        const sectionTitles =
            document.querySelectorAll(
                ".section-title"
            );


        sectionTitles.forEach(
            function (
                element
            ) {

                if (
                    isExcluded(
                        element
                    )
                ) {

                    return;

                }


                element.classList.add(
                    "reveal"
                );

            }
        );



        // ======================================================
        // SECTION SUBTITLE
        // ======================================================

        const sectionSubtitles =
            document.querySelectorAll(
                ".section-subtitle"
            );


        sectionSubtitles.forEach(
            function (
                element
            ) {

                if (
                    isExcluded(
                        element
                    )
                ) {

                    return;

                }


                element.classList.add(
                    "reveal"
                );

            }
        );



        // ======================================================
        // GAMBAR
        // ======================================================

        const images =
            document.querySelectorAll(
                "img"
            );


        images.forEach(
            function (
                image
            ) {

                if (
                    isExcluded(
                        image
                    )
                ) {

                    return;

                }


                /*
                 * Jangan animasikan logo/navbar.
                 */

                if (
                    image.closest(
                        ".navbar"
                    )
                    ||
                    image.closest(
                        ".cisalada-navbar"
                    )
                ) {

                    return;

                }


                image.classList.add(
                    "reveal-scale"
                );

            }
        );



        // ======================================================
        // HERO CONTENT
        // ======================================================

        const heroContent =
            document.querySelectorAll(
                ".hero-content"
            );


        heroContent.forEach(
            function (
                element
            ) {

                if (
                    isExcluded(
                        element
                    )
                ) {

                    return;

                }


                /*
                 * Hero sudah memiliki animasi dari CSS.
                 */

                element.classList.add(
                    "show"
                );

            }
        );

    }



    // ==========================================================
    // INTERSECTION OBSERVER
    // ==========================================================

    let observer;


    function createObserver()
    {

        observer =
            new IntersectionObserver(
                function (
                    entries,
                    observerInstance
                ) {


                    entries.forEach(
                        function (
                            entry
                        ) {


                            if (
                                !entry.isIntersecting
                            ) {

                                return;

                            }


                            /*
                             * Tambahkan class show.
                             */

                            entry.target.classList.add(
                                "show"
                            );


                            /*
                             * Setelah muncul,
                             * tidak perlu diamati lagi.
                             */

                            observerInstance.unobserve(
                                entry.target
                            );


                        }
                    );


                },
                {
                    root:
                        null,

                    rootMargin:
                        "0px 0px -8% 0px",

                    threshold:
                        0.12

                }
            );

    }



    // ==========================================================
    // MULAI OBSERVER
    // ==========================================================

    function observeElements()
    {

        if (
            !observer
        ) {

            return;

        }


        const animatedElements =
            document.querySelectorAll(
                ".reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-stagger"
            );


        animatedElements.forEach(
            function (
                element
            ) {

                /*
                 * Jangan observe loader / navbar.
                 */

                if (
                    isExcluded(
                        element
                    )
                ) {

                    return;

                }


                /*
                 * Kalau sudah show,
                 * biarkan tetap terlihat.
                 */

                if (
                    element.classList.contains(
                        "show"
                    )
                ) {

                    return;

                }


                observer.observe(
                    element
                );

            }
        );

    }



    // ==========================================================
    // REVEAL ELEMENT YANG SUDAH ADA DI LAYAR
    // ==========================================================

    function revealVisibleElements()
    {

        const elements =
            document.querySelectorAll(
                ".reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-stagger"
            );


        const viewportHeight =
            window.innerHeight
            ||
            document.documentElement.clientHeight;


        elements.forEach(
            function (
                element
            ) {

                if (
                    isExcluded(
                        element
                    )
                ) {

                    return;

                }


                const rect =
                    element.getBoundingClientRect();


                if (
                    rect.top
                    <
                    viewportHeight
                ) {

                    element.classList.add(
                        "show"
                    );

                    if (
                        observer
                    ) {

                        observer.unobserve(
                            element
                        );

                    }

                }

            }
        );

    }



    // ==========================================================
    // INITIALIZE
    // ==========================================================

    function initAnimations()
    {

        prepareElements();

        createObserver();

        observeElements();

        revealVisibleElements();

    }



    // ==========================================================
    // SETELAH DOM SIAP
    // ==========================================================

    if (
        document.readyState
        ===
        "loading"
    ) {

        document.addEventListener(
            "DOMContentLoaded",
            initAnimations
        );

    } else {

        initAnimations();

    }



    // ==========================================================
    // ELEMENT DINAMIS
    //
    // Berguna jika halaman memiliki:
    // - AJAX
    // - modal
    // - konten dinamis
    // ==========================================================

    const mutationObserver =
        new MutationObserver(
            function (
                mutations
            ) {

                let shouldRefresh =
                    false;


                mutations.forEach(
                    function (
                        mutation
                    ) {

                        if (
                            mutation.addedNodes
                            &&
                            mutation.addedNodes.length
                        ) {

                            shouldRefresh =
                                true;

                        }

                    }
                );


                if (
                    shouldRefresh
                ) {

                    prepareElements();

                    observeElements();

                }

            }
        );


    mutationObserver.observe(
        document.body,
        {
            childList:
                true,

            subtree:
                true

        }
    );



    // ==========================================================
    // RESIZE
    // ==========================================================

    let resizeTimer;


    window.addEventListener(
        "resize",
        function () {

            clearTimeout(
                resizeTimer
            );


            resizeTimer =
                setTimeout(
                    function () {

                        revealVisibleElements();

                    },
                    150
                );

        }
    );



})();