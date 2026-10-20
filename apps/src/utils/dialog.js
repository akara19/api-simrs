import { Alert, Platform } from 'react-native';

// Pesan informasi; onOk dijalankan setelah pengguna menekan OK
export function showAlert(title, message, onOk) {
  if (Platform.OS === 'web') {
    window.alert(`${title}\n\n${message}`);
    if (onOk) onOk();
  } else {
    Alert.alert(title, message, [{ text: 'OK', onPress: onOk }]);
  }
}

// Dialog konfirmasi; onConfirm dijalankan bila pengguna menyetujui
export function confirmDialog(title, message, onConfirm) {
  if (Platform.OS === 'web') {
    if (window.confirm(`${title}\n\n${message}`)) onConfirm();
  } else {
    Alert.alert(title, message, [
      { text: 'Batal', style: 'cancel' },
      { text: 'Hapus', style: 'destructive', onPress: onConfirm },
    ]);
  }
}
