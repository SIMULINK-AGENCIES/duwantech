# M-PESA Integration Setup Guide

## Environment Variables

Add the following variables to your `.env` file:

```env
# M-PESA Configuration
MPESA_BASE_URL=https://sandbox.safaricom.co.ke
MPESA_CONSUMER_KEY=gLrExVI9KRRVw34kNWvduRQAlLDtOXIyg4KyXVubIaIBFqNE
MPESA_CONSUMER_SECRET=ANgq8MV1ZOA2MKBdRGEuw8ubiAG2XPn4C3I8H71F14GBxd74QFIQwL4L650vG2VA
MPESA_PASSKEY=your_passkey_here
MPESA_SHORTCODE=your_shortcode_here
MPESA_ENVIRONMENT=sandbox
```

## Getting M-PESA Credentials

1. **Register for M-PESA Daraja API**:
   - Visit [Safaricom Developer Portal](https://developer.safaricom.co.ke/)
   - Create an account and register your application

2. **Get Your Credentials**:
   - Consumer Key and Consumer Secret from your app dashboard
   - Passkey from your app settings
   - Shortcode (Business Number) from Safaricom

3. **Environment Setup**:
   - For testing: Use sandbox environment
   - For production: Use production environment

## Testing the Integration

1. **Sandbox Testing**:
   - Use test phone numbers provided by Safaricom
   - Test with small amounts (KSh 1-10)
   - Check logs for callback responses

2. **Production Deployment**:
   - Update environment variables for production
   - Ensure HTTPS is enabled for callbacks
   - Test with real phone numbers

## Callback URL

The callback URL for M-PESA is: `https://yourdomain.com/mpesa/callback`

Make sure this URL is accessible and properly configured in your M-PESA app settings.

## Troubleshooting

- Check Laravel logs for M-PESA API responses
- Verify phone number format (should be 254XXXXXXXXX)
- Ensure callback URL is accessible from external sources
- Check if all environment variables are set correctly 