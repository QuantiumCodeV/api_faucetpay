<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address_regex');
            $table->boolean('require_dest_tag');
            $table->json('tickers');
            $table->decimal('min_receive', 20, 8);
            $table->decimal('min_withdraw', 20, 8);
            $table->decimal('max_withdraw_per_transaction', 20, 8);
            $table->integer('max_withdraw_transactions_per_day');
            $table->boolean('active');
            $table->boolean('send_active');
            $table->boolean('receive_active');
            $table->decimal('price', 20, 8)->default(0);
            $table->timestamps();
        });

        DB::table('currencies')->insert([
            [
                'name' => 'Bitcoin',
                'address_regex' => '^([13][a-km-zA-HJ-NP-Z1-9]{25,34})|^(bc1([qpzry9x8gf2tvdw0s3jn54khce6mua7l]{39}|[qpzry9x8gf2tvdw0s3jn54khce6mua7l]{59}))$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['BTC']),
                'min_receive' => 0.0005,
                'min_withdraw' => 0.0001,
                'max_withdraw_per_transaction' => 2,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Ethereum',
                'address_regex' => '^(?:0x)?[0-9a-fA-F]{40}$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['ETH']),
                'min_receive' => 0.005,
                'min_withdraw' => 0.001,
                'max_withdraw_per_transaction' => 100,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Tether ERC-20',
                'address_regex' => '^(?:0x)?[0-9a-fA-F]{40}$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['USDT', 'USDTERC', 'USDTERC20']),
                'min_receive' => 50,
                'min_withdraw' => 10,
                'max_withdraw_per_transaction' => 100000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'TRON',
                'address_regex' => '^[T][a-km-zA-HJ-NP-Z1-9]{25,34}$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['TRX']),
                'min_receive' => 20,
                'min_withdraw' => 10.000000,
                'max_withdraw_per_transaction' => 100000.000000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Tether TRC-20',
                'address_regex' => '^[T][a-km-zA-HJ-NP-Z1-9]{25,34}$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['USDTTRC', 'USDTTRC20']),
                'min_receive' => 10.000000,
                'min_withdraw' => 1,
                'max_withdraw_per_transaction' => 50000.000000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Toncoin',
                'address_regex' => '^[a-zA-Z0-9-_]{48}$',
                'require_dest_tag' => true,
                'tickers' => json_encode(['TON']),
                'min_receive' => 1,
                'min_withdraw' => 1,
                'max_withdraw_per_transaction' => 10000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Tether TON',
                'address_regex' => '^[a-zA-Z0-9-_]{48}$',
                'require_dest_tag' => true,
                'tickers' => json_encode(['USDTTON']),
                'min_receive' => 1,
                'min_withdraw' => 1,
                'max_withdraw_per_transaction' => 50000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'BNB BEP-20',
                'address_regex' => '^(?:0x)?[0-9a-fA-F]{40}$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['BNB20', 'BNBBEP20']),
                'min_receive' => 0.01,
                'min_withdraw' => 0.01000,
                'max_withdraw_per_transaction' => 100,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Tether BEP-20',
                'address_regex' => '^(?:0x)?[0-9a-fA-F]{40}$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['USDTBEP', 'USDTBEP20']),
                'min_receive' => 10,
                'min_withdraw' => 1,
                'max_withdraw_per_transaction' => 50000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Ripple',
                'address_regex' => '^r[1-9a-km-zA-HJ-NP-Z]{25,35}$',
                'require_dest_tag' => true,
                'tickers' => json_encode(['XRP']),
                'min_receive' => 10,
                'min_withdraw' => 1,
                'max_withdraw_per_transaction' => 50000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Solana',
                'address_regex' => '[1-9A-HJ-NP-Za-km-z]{32,44}',
                'require_dest_tag' => false,
                'tickers' => json_encode(['SOL']),
                'min_receive' => 0.1,
                'min_withdraw' => 0.05,
                'max_withdraw_per_transaction' => 1000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Litecoin',
                'address_regex' => '^((L|M|1|3)[a-km-zA-HJ-NP-Z1-9]{25,34})|^(ltc1([qpzry9x8gf2tvdw0s3jn54khce6mua7l]{39}|[qpzry9x8gf2tvdw0s3jn54khce6mua7l]{59}))$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['LTC']),
                'min_receive' => 0.025,
                'min_withdraw' => 0.010000,
                'max_withdraw_per_transaction' => 10000.000000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Dogecoin',
                'address_regex' => '^((D|A|9)[a-km-zA-HJ-NP-Z1-9]{25,34})$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['DOGE']),
                'min_receive' => 50,
                'min_withdraw' => 50,
                'max_withdraw_per_transaction' => 10000000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Monero',
                'address_regex' => '^(4|8)?[0-9A-Z]{1}[0-9a-zA-Z]{93}([0-9a-zA-Z]{11})?$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['XMR']),
                'min_receive' => 0.1,
                'min_withdraw' => 0.05,
                'max_withdraw_per_transaction' => 10000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Cardano',
                'address_regex' => '(^(addr|stake)1[ac-hj-np-z02-9]{6,}$)|(^(DdzFFz|Ae2td)[1-9A-HJ-NP-Za-km-z]+)',
                'require_dest_tag' => false,
                'tickers' => json_encode(['ADA']),
                'min_receive' => 2,
                'min_withdraw' => 2,
                'max_withdraw_per_transaction' => 500000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Dash',
                'address_regex' => '([X7][a-km-zA-HJ-NP-Z1-9]{25,34})$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['DASH', 'DSH']),
                'min_receive' => 0.05,
                'min_withdraw' => 0.05,
                'max_withdraw_per_transaction' => 10000.000000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Bitcoin Cash',
                'address_regex' => '^([13][a-km-zA-HJ-NP-Z1-9]{25,34})|^((bitcoincash:)?(q|p)[a-z0-9]{41})|^((BITCOINCASH:)?(Q|P)[A-Z0-9]{41})$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['BCH']),
                'min_receive' => 0.01,
                'min_withdraw' => 0.01,
                'max_withdraw_per_transaction' => 10000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Zcash',
                'address_regex' => '^(t1|t3)[a-km-zA-HJ-NP-Z1-9]{33}$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['ZEC', 'ZCASH']),
                'min_receive' => 0.05,
                'min_withdraw' => 0.05,
                'max_withdraw_per_transaction' => 10000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Notcoin',
                'address_regex' => '^[a-zA-Z0-9-_]{48}$',
                'require_dest_tag' => true,
                'tickers' => json_encode(['NOT']),
                'min_receive' => 50,
                'min_withdraw' => 100,
                'max_withdraw_per_transaction' => 1000000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Ethereum Classic',
                'address_regex' => '^(?:0x)?[0-9a-fA-F]{40}$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['ETC']),
                'min_receive' => 0.2,
                'min_withdraw' => 0.2,
                'max_withdraw_per_transaction' => 10000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'EOS',
                'address_regex' => '(^[a-z1-5.]{1,11}[a-z1-5]$)|(^[a-z1-5.]{12}[a-j1-5]$)',
                'require_dest_tag' => true,
                'tickers' => json_encode(['EOS']),
                'min_receive' => 1,
                'min_withdraw' => 1,
                'max_withdraw_per_transaction' => 10000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
                'price' => 0,
            ],
            [
                'name' => 'Stellar',
                'address_regex' => '^G[A-Z2-7]{55}$',
                'require_dest_tag' => true,
                'tickers' => json_encode(['XLM']),
                'min_receive' => 10,
                'min_withdraw' => 10,
                'max_withdraw_per_transaction' => 100000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
            ],
            [
                'name' => 'SHIBA INU BEP-20',
                'address_regex' => '^(?:0x)?[0-9a-fA-F]{40}$',
                'require_dest_tag' => false,
                'tickers' => json_encode(['SHIB', 'SHIBBEP20']),
                'min_receive' => 1000000,
                'min_withdraw' => 100000,
                'max_withdraw_per_transaction' => 100000000,
                'max_withdraw_transactions_per_day' => 1000000,
                'active' => true,
                'send_active' => true,
                'receive_active' => true,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
