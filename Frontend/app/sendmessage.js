import React, { useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet } from 'react-native';
import axios from 'axios';
import { SERVER_IP } from '@env';
import AsyncStorage from '@react-native-async-storage/async-storage';

const sendmessage = ({ route, navigation }) => {
  const [message, setMessage] = useState('');
  const { ownerId } = route.params; // Récupérez l'ID du propriétaire de la plante

  const handleSendMessage = async () => {
    try {
      const token = await AsyncStorage.getItem('loginToken');
      await axios.post(`http://${SERVER_IP}:8000/api/messages`, {
        id_utilisateur: ownerId,
        message,
      }, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      alert('Message envoyé avec succès');
      navigation.goBack(); // Retourner à l'écran précédent après l'envoi
    } catch (error) {
      console.error('Erreur lors de l\'envoi du message:', error);
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.label}>Envoyer un message:</Text>
      <TextInput
        style={styles.input}
        placeholder="Votre message"
        value={message}
        onChangeText={setMessage}
      />
      <Button title="Envoyer" onPress={handleSendMessage} />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    padding: 20,
  },
  label: {
    fontSize: 18,
    marginBottom: 10,
  },
  input: {
    height: 40,
    borderColor: 'gray',
    borderWidth: 1,
    marginBottom: 20,
    paddingHorizontal: 10,
  },
});

export default sendmessage;
