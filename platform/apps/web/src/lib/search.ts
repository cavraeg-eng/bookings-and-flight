export function inDays(n: number): string {
    const d = new Date();
    d.setDate(d.getDate() + n);

    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
}
