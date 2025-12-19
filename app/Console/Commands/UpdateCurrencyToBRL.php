<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Core\Repositories\ChannelRepository;
use Webkul\Core\Repositories\CurrencyRepository;
use Illuminate\Support\Facades\DB;

class UpdateCurrencyToBRL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update-to-brl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza todos os canais para usar BRL como moeda padrão';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ChannelRepository $channelRepository, CurrencyRepository $currencyRepository)
    {
        $this->info('Atualizando moeda padrão para BRL...');

        // Verificar se a moeda BRL existe
        $brlCurrency = $currencyRepository->findOneByField('code', 'BRL');

        if (!$brlCurrency) {
            $this->info('Moeda BRL não encontrada. Criando...');
            
            // Criar a moeda BRL
            $brlCurrency = $currencyRepository->create([
                'code' => 'BRL',
                'name' => 'Real Brasileiro',
                'symbol' => 'R$',
                'decimal' => 2,
                'group_separator' => '.',
                'decimal_separator' => ',',
                'currency_position' => 'left',
            ]);

            $this->info('Moeda BRL criada com sucesso!');
        } else {
            $this->info('Moeda BRL já existe no sistema. Atualizando configurações...');
            
            // Atualizar a moeda BRL com as configurações corretas
            $currencyRepository->update([
                'name' => 'Real Brasileiro',
                'symbol' => 'R$',
                'decimal' => 2,
                'group_separator' => '.',
                'decimal_separator' => ',',
                'currency_position' => 'left',
            ], $brlCurrency->id);
            
            // Recarregar a moeda atualizada
            $brlCurrency = $currencyRepository->find($brlCurrency->id);
            
            $this->info('Configurações da moeda BRL atualizadas!');
        }

        // Atualizar todos os canais
        $channels = $channelRepository->all();

        if ($channels->isEmpty()) {
            $this->warn('Nenhum canal encontrado.');
            return Command::FAILURE;
        }

        foreach ($channels as $channel) {
            $this->info("Atualizando canal: {$channel->code}...");

            // Carregar relacionamentos necessários
            $channel->load(['currencies', 'locales', 'inventory_sources']);

            // Obter moedas atuais do canal
            $currentCurrencies = $channel->currencies->pluck('id')->toArray();

            // Adicionar BRL às moedas permitidas se não estiver presente
            if (!in_array($brlCurrency->id, $currentCurrencies)) {
                $currentCurrencies[] = $brlCurrency->id;
            }

            // Obter locales e inventory sources atuais
            $currentLocales = $channel->locales->pluck('id')->toArray();
            $currentInventorySources = $channel->inventory_sources->pluck('id')->toArray();

            // Atualizar o canal
            $channelRepository->update([
                'base_currency_id' => $brlCurrency->id,
                'currencies' => $currentCurrencies,
                'locales' => $currentLocales,
                'inventory_sources' => $currentInventorySources,
            ], $channel->id);

            $this->info("Canal {$channel->code} atualizado com sucesso!");
        }

        $this->info('Todos os canais foram atualizados para usar BRL como moeda padrão!');
        
        return Command::SUCCESS;
    }
}
