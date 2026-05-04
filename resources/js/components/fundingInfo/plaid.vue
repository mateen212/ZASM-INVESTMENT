<template>
  <div>
    <!-- Connect Bank Button -->
    <button @click="connectPlaid" :disabled="!linkToken">Connect Bank</button>

    <!-- Display Bank Accounts as Radio Buttons -->
    <div v-if="accounts.length > 0">
      <h3>Select a Bank Account</h3>
      <div v-for="account in accounts" :key="account.account_id">
        <input type="radio" :value="account.account_id" v-model="selectedAccountId" :id="account.account_id" />
        <label :for="account.account_id">
          {{ account.name }} ({{ account.official_name || 'N/A' }}) -
          {{ account.subtype }} (****{{ account.mask }})
        </label>
      </div>
    </div>

    <!-- Add Funds Button -->
    <button v-if="selectedAccountId" @click="addFunds" :disabled="isProcessing">
      Add Funds
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const linkToken = ref(null);
const accounts = ref([]);
const selectedAccountId = ref(null);
const accessToken = ref(null); // Add to store access_token
const isProcessing = ref(false);;

onMounted(async () => {
  try {
    let url = window.baseUrl;
    const res = await fetch(url + '/admin/api/plaid/link-token', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'include',
    });

    if (!res.ok) throw new Error('Failed to fetch link token');

    const data = await res.json();
    linkToken.value = data.link_token;
  } catch (error) {
    console.error('Failed to fetch link token:', error);
  }
});

async function connectPlaid() {
  if (!linkToken.value) {
    console.error('Link token not loaded');
    return;
  }

  const handler = window.Plaid.create({
    token: linkToken.value,
    onSuccess: async (public_token, metadata) => {
      try {
        let url = window.baseUrl;
        // Exchange public token for access token
        const exchangeRes = await fetch(url + '/admin/api/plaid/exchange-token', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          credentials: 'include',
          body: JSON.stringify({ public_token }),
        });

        const exchangeData = await exchangeRes.json();
        if (!exchangeData.access_token) throw new Error('Failed to exchange token');

        // Store access token
        accessToken.value = exchangeData.access_token;

        // Fetch accounts using access token
        const accountsRes = await fetch(url + '/admin/api/plaid/accounts', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          credentials: 'include',
          body: JSON.stringify({ access_token: exchangeData.access_token }),
        });

        const accountsData = await accountsRes.json();
        if (accountsData.accounts) {
          accounts.value = accountsData.accounts;
          console.log('Accounts fetched:', accountsData.accounts);
        } else {
          throw new Error('No accounts found');
        }
      } catch (err) {
        console.error('Error exchanging token or fetching accounts:', err);
      }
    },
    onExit: (err, metadata) => {
      if (err) {
        console.error('Plaid exit error:', err);
      } else {
        console.log('Plaid exited:', metadata);
      }
    },
  });

  handler.open();
}

async function addFunds() {
  if (!selectedAccountId.value || !accessToken.value) {
    console.error('No account selected or access token missing');
    return;
  }

  isProcessing.value = true;
  try {
    let url = window.baseUrl;
    // Send selected account ID and access token to backend
    const res = await fetch(url + '/admin/api/plaid/create-payment', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      credentials: 'include',
      body: JSON.stringify({
        account_id: selectedAccountId.value,
        access_token: accessToken.value, // Use stored access token
        amount: 100, // Example amount, adjust as needed
      }),
    });

    const data = await res.json();
    if (data.success) {
      console.log('Payment initiated:', data);
    } else {
      throw new Error(data.error || 'Failed to initiate payment');
    }
  } catch (err) {
    console.error('Error initiating payment:', err);
  } finally {
    isProcessing.value = false;
  }
}
</script>