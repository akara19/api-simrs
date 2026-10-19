import React, {useCallback,useState} from "react";
import {View,Text,FlatList,TouchableOpacity,StyleSheet, ActivityIndicator,} from 'react-native';
import {  useFocusEffect} from '@react-navigation/native';
import { showAlert, confirmDialog } from '../utils/dialog';

export default function makeListScreen({ api, formRoute, titleOf, subOf, filterBy, actions }) {
  return function ListScreen({ navigation, route }) {
    const filterValue = filterBy ? route.params?.[filterBy] : undefined;
    const defaults = filterBy ? { [filterBy]: String(filterValue) } : undefined;
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);

    const loadData = async () => {
      setLoading(true);
      try {
        let rows = await api.list();
        if (filterBy) rows = rows.filter((r) => String(r[filterBy]) === String(filterValue));
        setData(rows);
      } catch (e) {
        showAlert('Gagal memuat', e.message);
      } finally {
        setLoading(false);
      }
    };

    useFocusEffect(useCallback(() => { loadData(); }, []));

    const handleDelete = (item) =>
      confirmDialog('Hapus Data', `Yakin menghapus "${titleOf(item)}"?`, async () => {
        try {
          await api.remove(item.id);
          loadData();
        } catch (e) {
          showAlert('Gagal menghapus', e.message); // mis. ditolak karena relasi RESTRICT
        }
      });

    const renderItem = ({ item }) => {
      const extra = actions ? actions(item, { reload: loadData, navigation }) : [];
      const buttons = [
        { label: 'Edit', color: '#f59e0b', onPress: () => navigation.navigate(formRoute, { item,
defaults }) },
        ...extra,
        { label: 'Hapus', color: '#ef4444', onPress: () => handleDelete(item) },
      ];
      return (
        <View style={s.card}>
          <Text style={s.title}>{titleOf(item)}</Text>
          <Text style={s.sub}>{subOf(item)}</Text>
          <View style={s.btnRow}>
            {buttons.map((b) => (
              <TouchableOpacity key={b.label} style={[s.btn, { backgroundColor: b.color }]}
onPress={b.onPress}>
                <Text style={s.btnText}>{b.label}</Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>
      );
    };

    return (
      <View style={s.container}>
        {loading && <ActivityIndicator size="large" color="#2563eb" />}
        <FlatList
          data={data}
          keyExtractor={(item) => String(item.id)}
          renderItem={renderItem}
          refreshing={loading}
          onRefresh={loadData}
          ListEmptyComponent={!loading && <Text style={s.empty}>Belum ada data</Text>}
        />
        <TouchableOpacity style={s.fab} onPress={() => navigation.navigate(formRoute, { defaults })}>
          <Text style={s.fabText}>＋</Text>
        </TouchableOpacity>
      </View>
    );
  };
}

const s = StyleSheet.create({
  container: { flex: 1, padding: 12, backgroundColor: '#f3f4f6' },
  card: { backgroundColor: '#fff', padding: 14, borderRadius: 10, marginBottom: 10, elevation: 2 },
  title: { fontSize: 16, fontWeight: 'bold' },
  sub: { color: '#6b7280', marginTop: 2 },
  btnRow: { flexDirection: 'row', flexWrap: 'wrap', marginTop: 10 },
  btn: { paddingVertical: 7, paddingHorizontal: 12, borderRadius: 6, marginRight: 6, marginBottom: 4
},
  btnText: { color: '#fff', fontWeight: '600' },
  empty: { textAlign: 'center', marginTop: 30, color: '#6b7280' },
  fab: {
    position: 'absolute', right: 20, bottom: 24, width: 56, height: 56, borderRadius: 28,
    backgroundColor: '#16a34a', alignItems: 'center', justifyContent: 'center', elevation: 5,
  },
  fabText: { color: '#fff', fontSize: 28, marginTop: -2 },
});
