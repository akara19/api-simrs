export const required = (label) => (v) =>
  String(v ?? '').trim() === '' ? `${label} wajib diisi` : '';

export const exactDigits = (label, n) => (v) =>
  /^\d+$/.test(v) && v.length === n ? '' : `${label} harus ${n} digit angka`;

export const positiveInt = (label) => (v) =>
  /^\d+$/.test(String(v)) && Number(v) > 0 ? '' : `${label} harus bilangan bulat lebih dari 0`;

export const isDate = (label) => (v) => {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(v)) return `${label} berformat YYYY-MM-DD`;
  const d = new Date(v);
  return isNaN(d) || d.toISOString().slice(0, 10) !== v
    ? `${label} bukan tanggal yang valid` : '';
};
