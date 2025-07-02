wp.domReady(function () {
    const { subscribe, select } = wp.data;

    const hideElements = () => {
        // Check for Classic Editor elements first
        const classicEditorBody = document.getElementById('post-body');
        const isClassicEditorActive = classicEditorBody !== null;

        if (isClassicEditorActive) {
            if (eucSettings.classicCssSelectors && eucSettings.classicCssSelectors.length > 0) {
                const css = eucSettings.classicCssSelectors.map(selector => `${selector} { display: none !important; }`).join('\n');
                let styleTag = document.getElementById('euc-classic-css-styles');
                if (!styleTag) {
                    styleTag = document.createElement('style');
                    styleTag.id = 'euc-classic-css-styles';
                    document.head.appendChild(styleTag);
                }
                styleTag.textContent = css;
            }
        } else if (eucSettings.isBlockEditorScreen) {
            if (eucSettings.blockPanelNames && eucSettings.blockPanelNames.length > 0) {
                eucSettings.blockPanelNames.forEach(item => {
                    if (item.startsWith('.') || item.startsWith('#')) {
                        // It's a direct CSS selector
                        const elements = document.querySelectorAll(item);
                        elements.forEach(el => {
                            el.style.display = 'none';
                        });
                    } else {
                        // It's a panel title
                        const panelNodes = document.querySelectorAll('.editor-post-panel__row');
                        panelNodes.forEach(panelNode => {
                            const titleElement = panelNode.querySelector('.editor-post-panel__row-label');
                            if (titleElement) {
                                const panelTitle = titleElement.innerText.trim();
                                if (item === panelTitle) {
                                    panelNode.style.display = 'none';
                                }
                            }
                        });
                    }
                });
            }
        }

        // Always apply custom CSS if present
        if (eucSettings.customCss) {
            let customStyleTag = document.getElementById('euc-custom-css-styles');
            if (!customStyleTag) {
                customStyleTag = document.createElement('style');
                customStyleTag.id = 'euc-custom-css-styles';
                document.head.appendChild(customStyleTag);
            }

            const customCssSelectors = eucSettings.customCss.split(/\r?\n/).filter(line => line.trim() !== '');
            if (customCssSelectors.length > 0) {
                const css = customCssSelectors.map(selector => `${selector} { display: none !important; }`).join('\n');
                customStyleTag.textContent = css;
            } else {
                customStyleTag.textContent = '';
            }
        }
    };

    // Initial hiding when the DOM is ready.
    hideElements();

    // Subscribe to editor data changes to catch dynamically rendered panels.
    subscribe(() => {
        hideElements();
    });
});