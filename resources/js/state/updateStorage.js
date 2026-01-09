import { storage } from "../helpers/storage";

export function updateGlobalStorageKey(key, value) {
    let existingStorage = JSON.parse(storage.get(quizGlobalStorageKey, 'session'));
    existingStorage = existingStorage === null ? {} : existingStorage;
    existingStorage[key] = value;
    storage.set(quizGlobalStorageKey, JSON.stringify(existingStorage), 'session');
}
