export const generateUUID = (): string => {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        const v = c === 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
};

export const formatFileSize = (bytes: number): string => {
    const kb = bytes / 1024;
    const mb = kb / 1024;

    if (mb >= 1) {
        return `${mb.toFixed(2)} MB`;
    }
    return `${kb.toFixed(2)} KB`;
};

export const getCurrentDate = () => {
    const now = new Date();
    const day = now.getDate();
    const month = now.toLocaleString('en-US', { month: 'long' });
    const year = now.getFullYear();
    return `${day} ${month} ${year}`;
};
