import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, FlatList } from 'react-native';
import axios from 'axios';
import { SERVER_IP } from '@env';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function messagerie() {
  const [userMessages, setUserMessages] = useState([]);

  useEffect(() => {
    getUserMessages();
  }, []);

  const getUserMessages = async () => {
    try {
      const token = await AsyncStorage.getItem('loginToken');
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      const response = await axios.get(`http://${SERVER_IP}:8000/api/messages/get-user-messages`);
      setUserMessages(response.data);
    } catch (error) {
      console.error('Erreur lors de la récupération des messages:', error);
    }
  };

  const renderMessage = ({ item }) => (
    <View style={styles.messageContainer}>
      <View style={styles.messageDetails}>
        <Text style={styles.messageText}>De: {item.senderName}</Text>
        <Text style={styles.messageText}>Message: {item.message}</Text>
        <Text style={styles.messageDate}>Date: {new Date(item.date).toLocaleString()}</Text>
      </View>
    </View>
  );

  return (
    <View style={styles.container}>
      <FlatList
        data={userMessages}
        renderItem={renderMessage}
        keyExtractor={(item, index) => index.toString()}
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
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f0f0f0',
    borderRadius: 10,
    marginBottom: 10,
    padding: 10,
    width: '100%',
  },
  messageDetails: {
    flex: 1,
  },
  messageText: {
    fontSize: 16,
    marginBottom: 5,
  },
  messageDate: {
    fontSize: 14,
    color: 'gray',
  },
});
