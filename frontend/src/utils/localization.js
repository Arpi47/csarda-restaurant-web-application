export function localizedField(item, field, language) {
    if (!item) return "";

    const key = `${field}_${language}`;

    return item[key] ?? item[`${field}_hu`];
}
