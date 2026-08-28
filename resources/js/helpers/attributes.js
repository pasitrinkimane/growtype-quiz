export function getAttrValue(element, attributeName) {
    if (!element || typeof element.attr !== 'function') {
        return '';
    }

    const value = element.attr(attributeName);

    return typeof value === 'string' ? value : '';
}
