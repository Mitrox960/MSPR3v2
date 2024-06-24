import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity } from 'react-native';
import axios from 'axios';
import { SERVER_IP } from '@env';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function MessagerieScreen() {
  const [messages, setMessages] = useState([]);

  useEffect(() => {
    getUserMessages();
  }, []);

  const getUserMessages = async () => {
    try {
      const token = await AsyncStorage.getItem('loginToken');
      const userId = await AsyncStorage.getItem('userId'); // Assurez-vous que userId est stocké dans AsyncStorage lors de la connexion
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      const response = await axios.get(`http://${SERVER_IP}:8000/api/messages/${userId}`);
      setMessages(response.data);
    } catch (error) {
      console.error('Erreur lors de la récupération des messages:', error);
    }
  };

  return (
    <View style={styles.container}>
      <FlatList
        data={messages}
        renderItem={({ item }) => (
          <View style={styles.messageContainer}>
            <Text style={styles.messageText}>{item.message}</Text>
            <Text style={styles.timestampText}>{new Date(item.created_at).toLocaleString()}</Text>
          </View>
        )}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  listContent: {
    marginTop: 20,
    alignItems: 'center',
    paddingHorizontal: 10,
  },
  messageContainer: {
    backgroundColor: '#f0f0f0',
    borderRadius: 10,
    marginBottom: 10,
    padding: 10,
    width: '100%',
  },
  messageText: {
    fontSize: 16,
    marginBottom: 5,
  },
  timestampText: {
    fontSize: 12,
    color: 'gray',
  },
});
