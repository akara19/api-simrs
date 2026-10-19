import { BASE_URL } from '../config/api';

const q = encodeURIComponent;

async function request(url, options) {
  const res = await fetch(url, options);
  let json;
  try {
    json = await res.json();
  } catch (e) {
    throw new Error('Respons server bukan JSON. Periksa error PHP.');
  }
  if (!res.ok || json.status !== 'success') {
    throw new Error(json.message || 'Terjadi kesalahan pada server');
  }
  return json;
}

const toQuery = (obj) =>
  Object.entries(obj).map(([k, v]) => `${k}=${q(v ?? '')}`).join('&');

const jsonOpts = (body) => ({
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(body),
});

// updateVia: 'query' (seperti poli/update.php) atau 'json'
export function createCrudService(resource, { updateVia = 'query' } = {}) {
  const url = (file, qs = '') => `${BASE_URL}/${resource}/${file}.php${qs}`;
  return {
    list: async () => {
      const data = (await request(url('index'))).data;
      return Array.isArray(data) ? data : [];
    },
    create: (body) => request(url('create'), jsonOpts(body)),
    update: (id, body) =>
      updateVia === 'json'
        ? request(url('update'), jsonOpts({ id, ...body }))
        : request(url('update', `?${toQuery({ id, ...body })}`), { method: 'POST' }),
    remove: (id) => request(url('delete', `?id=${id}`), { method: 'POST' }),
  };
}

src/services/apis