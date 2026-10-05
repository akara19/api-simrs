import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import PoliListScreen from './src/screens/PoliListScreen';
import PoliFormScreen from './src/screens/PoliFormScreen';

const Stack = createNativeStackNavigator();

export default function App() {
  return (
    <NavigationContainer>
      <Stack.Navigator>
<Stack.Screen name="PoliList" component={PoliListScreen} options={{ title: 'Data Poli' }} />
        <Stack.Screen
          name="PoliForm"
          component={PoliFormScreen}
          options={({ route }) => ({
            title: route.params?.poli ? 'Ubah Poli' : 'Tambah Poli',
          })}
        />
      </Stack.Navigator>
    </NavigationContainer>
  );
}
