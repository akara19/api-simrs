import { Platform } from 'react-native';

// Ganti 192.168.1.10 dengan IP LAN komputer Anda (cek: ipconfig / ifconfig)
const LAN_IP = '192.168.110.160';
const USE_REAL_DEVICE = false; // true bila memakai Expo Go di ponsel fisik

const HOST = USE_REAL_DEVICE
  ? LAN_IP
  : Platform.OS === 'android' ? '10.0.2.2' : 'localhost';

export const BASE_URL = `http://${HOST}:8080/api-simrs`;
