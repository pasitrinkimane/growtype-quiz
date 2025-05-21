export function updateGlobalStorageKey(key, value) {
    let existingStorage = JSON.parse(sessionStorage.getItem(quizGlobalStorageKey));
    existingStorage = existingStorage === null ? {} : existingStorage;
    existingStorage[key] = value;
    sessionStorage.setItem(quizGlobalStorageKey, JSON.stringify(existingStorage));
}
