function nestedAnswerValues(value, output = []) {
    if (Array.isArray(value)) {
        value.forEach((item) => nestedAnswerValues(item, output));
    } else if (value && typeof value === 'object') {
        Object.values(value).forEach((item) => nestedAnswerValues(item, output));
    } else if (typeof value === 'string') {
        output.push(value);
    }

    return output;
}

function elementHasVisibleContent(element) {
    const styles = window.getComputedStyle(element);

    if (element.hidden || styles.display === 'none' || styles.visibility === 'hidden') {
        return false;
    }

    const children = Array.from(element.children);

    return children.length === 0 || children.some((child) => elementHasVisibleContent(child));
}

function activeQuestionIsReady(modal) {
    const activeQuestion = modal.querySelector('.growtype-quiz-question.is-active');

    if (!activeQuestion) {
        return false;
    }

    const styles = window.getComputedStyle(activeQuestion);

    return styles.display !== 'none'
        && styles.visibility !== 'hidden'
        && Number.parseFloat(styles.opacity || '1') >= 0.999;
}

function syncQuizHeaderVisibility(modal) {
    const questionIsReady = activeQuestionIsReady(modal);
    let shouldRetry = false;

    modal.querySelectorAll('.growtype-quiz-header').forEach((header) => {
        const hasVisibleContent = Array.from(header.children)
            .some((child) => elementHasVisibleContent(child));
        const hasBeenRevealed = header.dataset.growtypeQuizHeaderRevealed === 'true';
        const isWaitingForInitialReveal = hasVisibleContent && !questionIsReady && !hasBeenRevealed;
        const shouldBeHidden = !hasVisibleContent || isWaitingForInitialReveal;

        if (isWaitingForInitialReveal) {
            shouldRetry = true;
        }

        if (header.hidden !== shouldBeHidden) {
            header.hidden = shouldBeHidden;

            if (shouldBeHidden) {
                header.classList.remove('is-revealing');
            } else {
                if (!hasBeenRevealed) {
                    header.dataset.growtypeQuizHeaderRevealed = 'true';
                    header.classList.add('is-revealing');
                }
            }
        }
    });

    return shouldRetry;
}

export function questionnaireModal() {
    const modals = Array.from(document.querySelectorAll('[data-growtype-quiz-modal]'));

    modals.forEach((modal) => {
        const content = modal.querySelector('.growtype-quiz-questionnaire-content');
        const initialContent = content.innerHTML;
        const modalSlug = modal.getAttribute('data-growtype-quiz-modal');
        const selector = modal.getAttribute('data-trigger-selector');
        const developmentPreview = modal.getAttribute('data-development-preview') === 'true';
        const showOnce = modal.getAttribute('data-show-once') === 'true';
        const shownStorageKey = `growtype_quiz_modal_shown_${modalSlug}`;
        const autoOpen = modal.getAttribute('data-auto-open') === 'true';
        const autoOpenDelay = Math.max(0, Number.parseInt(modal.getAttribute('data-auto-open-delay') || '0', 10) || 0);
        const autoOpenWaitFor = modal.getAttribute('data-auto-open-wait-for') || '';
        let origin = null;
        let originalAction = null;
        let resetOnHidden = false;
        let headerSyncFrame = null;
        let closeTimer = null;
        let autoOpenTimer = null;
        let autoOpenObserver = null;
        let autoOpenCompleted = false;
        let missingModalControllerWasReported = false;

        const hasBeenShown = () => {
            if (!showOnce || developmentPreview) {
                return false;
            }

            if (modal.dataset.growtypeQuizModalShown === 'true') {
                return true;
            }

            try {
                return window.localStorage.getItem(shownStorageKey) === 'true';
            } catch (error) {
                return false;
            }
        };

        const markAsShown = () => {
            if (!showOnce || developmentPreview) {
                return;
            }

            modal.dataset.growtypeQuizModalShown = 'true';

            try {
                window.localStorage.setItem(shownStorageKey, 'true');
            } catch (error) {
                // The in-page marker still prevents repeat opens when storage is unavailable.
            }
        };

        const getGlobalBootstrapModal = () => window.bootstrap?.Modal || null;

        const reportMissingModalController = () => {
            if (missingModalControllerWasReported) {
                return;
            }

            missingModalControllerWasReported = true;
            console.error('[growtypeQuiz] Bootstrap modal controller is unavailable.');
        };

        const showModal = (trigger = null) => {
            if (window.growtypeModal?.show) {
                window.growtypeModal.show(modal);
                return true;
            }

            const BootstrapModal = getGlobalBootstrapModal();
            if (BootstrapModal) {
                BootstrapModal.getOrCreateInstance(modal).show(trigger);
                return true;
            }

            reportMissingModalController();
            return false;
        };

        const hideModal = () => {
            if (window.growtypeModal?.hide) {
                window.growtypeModal.hide(modal);
                return true;
            }

            const BootstrapModal = getGlobalBootstrapModal();
            const modalInstance = BootstrapModal?.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
                return true;
            }

            reportMissingModalController();
            return false;
        };

        const scheduleQuizHeaderSync = () => {
            if (headerSyncFrame !== null) {
                return;
            }

            headerSyncFrame = window.requestAnimationFrame(() => {
                headerSyncFrame = null;
                if (syncQuizHeaderVisibility(modal)) {
                    scheduleQuizHeaderSync();
                }
            });
        };

        const headerObserver = new MutationObserver(scheduleQuizHeaderSync);
        modal.querySelectorAll('.growtype-quiz-header').forEach((header) => {
            header.hidden = true;
        });
        headerObserver.observe(modal, {
            attributes: true,
            attributeFilter: ['class', 'style', 'hidden'],
            childList: true,
            subtree: true
        });
        scheduleQuizHeaderSync();

        try {
            modal.completionActions = JSON.parse(modal.getAttribute('data-completion-actions') || '{}');
        } catch (error) {
            modal.completionActions = {};
        }

        const close = () => {
            hideModal();
        };

        modal.addEventListener('click', (event) => {
            const actionTarget = event.target.closest('[data-modal-action]');
            const action = actionTarget?.getAttribute('data-modal-action') || '';

            if (action !== 'close') {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            close();
        }, true);

        const open = (trigger) => {
            origin = trigger;
            const form = trigger.closest('form');

            originalAction = form ? {type: 'form', form} : {
                type: 'url',
                url: trigger.getAttribute('href') || trigger.getAttribute('data-original-url') || ''
            };

            showModal(trigger);
        };

        document.addEventListener('click', (event) => {
            let trigger = null;

            try {
                trigger = selector ? event.target.closest(selector) : null;
            } catch (error) {
                return;
            }

            if (!trigger || trigger.dataset.growtypeQuizModalBypass === 'true') {
                return;
            }

            if (hasBeenShown()) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            open(trigger);
        }, true);

        modal.addEventListener('shown.bs.modal', (event) => {
            markAsShown();
            scheduleQuizHeaderSync();
            modal.dispatchEvent(new CustomEvent('growtypeQuizModalOpened', {
                bubbles: true,
                detail: {trigger: event.relatedTarget || origin}
            }));
        });

        modal.addEventListener('hidden.bs.modal', () => {
            if (closeTimer !== null) {
                window.clearTimeout(closeTimer);
                closeTimer = null;
            }

            if (origin && typeof origin.focus === 'function') {
                origin.focus();
            }

            if (!resetOnHidden) {
                return;
            }

            resetOnHidden = false;
            content.innerHTML = initialContent;
            scheduleQuizHeaderSync();
            document.dispatchEvent(new CustomEvent('growtypeQuizReinitialize', {
                detail: {modalSlug}
            }));
        });

        document.addEventListener('growtypeQuizSaved', (event) => {
            const quizWrapper = modal.querySelector('.growtype-quiz-wrapper');
            if (!quizWrapper || event.detail.id !== quizWrapper.id) {
                return;
            }

            const values = nestedAnswerValues(event.detail.answers);
            const matchedValue = Object.keys(modal.completionActions).find((value) => values.includes(value));
            const action = String(matchedValue
                ? modal.completionActions[matchedValue]
                : (modal.getAttribute('data-default-action') || 'close'));

            modal.dispatchEvent(new CustomEvent('growtypeQuizModalCompleted', {
                bubbles: true,
                detail: {answers: event.detail.answers, response: event.detail.response, action}
            }));

            if (action === 'continue_original' && originalAction) {
                if (originalAction.type === 'form') {
                    HTMLFormElement.prototype.submit.call(originalAction.form);
                } else if (originalAction.url) {
                    window.location.assign(originalAction.url);
                }
                return;
            }

            if (action.indexOf('dispatch:') === 0) {
                document.dispatchEvent(new CustomEvent(action.substring(9), {
                    detail: {answers: event.detail.answers, response: event.detail.response}
                }));
            }

            resetOnHidden = true;
            const activeQuestion = modal.querySelector('.growtype-quiz-question.is-active');
            const closeAfter = Number.parseInt(activeQuestion?.dataset.modalCloseAfter || '', 10);

            if (Number.isFinite(closeAfter) && closeAfter > 0) {
                closeTimer = window.setTimeout(() => {
                    closeTimer = null;
                    close();
                }, closeAfter);
                return;
            }

            close();
        });

        if (autoOpen) {
            if (hasBeenShown()) {
                modal.dataset.autoOpenState = 'shown-before';
                return;
            }

            const matchesAutoOpenWaitSelector = () => {
                if (!autoOpenWaitFor) {
                    return false;
                }

                try {
                    return Array.from(document.querySelectorAll(autoOpenWaitFor))
                        .some((element) => element !== modal);
                } catch (error) {
                    return false;
                }
            };

            const otherModalIsActive = () => Array.from(document.querySelectorAll('.modal'))
                .some((element) => element !== modal && (
                    element.classList.contains('show')
                    || element.getAttribute('aria-modal') === 'true'
                ));

            const autoOpenIsBlocked = () => matchesAutoOpenWaitSelector()
                || otherModalIsActive()
                || document.querySelector('.modal-backdrop') !== null;

            const clearAutoOpenTimer = () => {
                if (autoOpenTimer !== null) {
                    window.clearTimeout(autoOpenTimer);
                    autoOpenTimer = null;
                }
            };

            const finishAutoOpen = () => {
                autoOpenCompleted = true;
                modal.dataset.autoOpenState = 'opening';
                clearAutoOpenTimer();

                if (autoOpenObserver) {
                    autoOpenObserver.disconnect();
                    autoOpenObserver = null;
                }

                document.removeEventListener('show.bs.modal', handleOtherModalShow);
                document.removeEventListener('hidden.bs.modal', scheduleAutoOpen);
                showModal();
            };

            const scheduleAutoOpen = () => {
                if (autoOpenCompleted || hasBeenShown()) {
                    autoOpenCompleted = true;
                    modal.dataset.autoOpenState = 'shown-before';
                    clearAutoOpenTimer();

                    if (autoOpenObserver) {
                        autoOpenObserver.disconnect();
                        autoOpenObserver = null;
                    }

                    document.removeEventListener('show.bs.modal', handleOtherModalShow);
                    document.removeEventListener('hidden.bs.modal', scheduleAutoOpen);
                    return;
                }

                if (autoOpenIsBlocked()) {
                    modal.dataset.autoOpenState = 'blocked';
                    clearAutoOpenTimer();
                    return;
                }

                if (autoOpenTimer !== null) {
                    return;
                }

                modal.dataset.autoOpenState = 'waiting';
                autoOpenTimer = window.setTimeout(() => {
                    autoOpenTimer = null;

                    if (autoOpenIsBlocked()) {
                        scheduleAutoOpen();
                        return;
                    }

                    finishAutoOpen();
                }, autoOpenDelay);
            };

            const handleOtherModalShow = (event) => {
                if (event.target !== modal) {
                    clearAutoOpenTimer();
                    window.setTimeout(scheduleAutoOpen, 0);
                }
            };

            autoOpenObserver = new MutationObserver(scheduleAutoOpen);
            autoOpenObserver.observe(document.body, {
                attributes: true,
                attributeFilter: ['class', 'style', 'aria-modal'],
                childList: true,
                subtree: true
            });

            document.addEventListener('show.bs.modal', handleOtherModalShow);
            document.addEventListener('hidden.bs.modal', scheduleAutoOpen);
            scheduleAutoOpen();
        }
    });
}
