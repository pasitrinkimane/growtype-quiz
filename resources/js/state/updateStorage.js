export function updateGlobalStorageKey(key, value) {
    let existingStorage = JSON.parse(sessionStorage.getItem('growtype_quiz_global'));
    existingStorage[key] = value;
    sessionStorage.setItem('growtype_quiz_global', JSON.stringify(existingStorage));
}
