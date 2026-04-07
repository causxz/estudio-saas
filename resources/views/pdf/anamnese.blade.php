<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Anamnese - {{ $record->client->name }}</title>
    <style>
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 13px; 
            color: #333; 
            line-height: 1.5; 
        }
        .container { 
            width: 100%; 
            margin: 0 auto; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 24px; 
            color: #bd6a82; /* Tom de rosa queimado elegante */
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        .header p { 
            margin: 5px 0 0 0; 
            font-size: 14px; 
            color: #777; 
        }
        .section-title { 
            background-color: #fce8ee; 
            color: #a8546d; 
            padding: 8px 12px; 
            font-weight: bold; 
            margin-top: 20px; 
            border-radius: 4px; 
            text-transform: uppercase; 
            font-size: 12px;
            border-left: 4px solid #bd6a82;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        td { 
            padding: 8px 12px; 
            border-bottom: 1px solid #f2f2f2; 
            vertical-align: top; 
        }
        .label { 
            font-weight: bold; 
            width: 40%; 
            color: #555; 
        }
        .value { 
            width: 60%; 
            color: #222;
        }
        .checkbox-list td { 
            border: 1px solid #f2f2f2; 
            padding: 8px 12px; 
            width: 50%; 
        }
        .checkbox-list strong {
            display: block;
            margin-bottom: 4px;
            color: #555;
        }
        .signature-box { 
            margin-top: 80px; 
            text-align: center; 
        }
        .signature-line { 
            border-top: 1px solid #000; 
            width: 350px; 
            margin: 0 auto; 
            padding-top: 5px; 
            font-weight: bold;
        }
        .footer { 
            margin-top: 40px; 
            text-align: center; 
            font-size: 10px; 
            color: #aaa; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ficha de Anamnese</h1>
            <p>Extensão de Cílios | {{ $record->studio->name ?? 'Estúdio Matriz' }}</p>
        </div>

        <div class="section-title">Dados da Cliente</div>
        <table>
            <tr>
                <td class="label">Nome Completo:</td>
                <td class="value">{{ $record->client->name }}</td>
            </tr>
            <tr>
                <td class="label">WhatsApp:</td>
                <td class="value">{{ $record->client->whatsapp ?? 'Não informado' }}</td>
            </tr>
            <tr>
                <td class="label">Data do Preenchimento:</td>
                <td class="value">{{ $record->created_at->format('d/m/Y') }}</td>
            </tr>
        </table>

        <div class="section-title">Questionário de Saúde e Hábitos</div>
        <table class="checkbox-list">
            <tr>
                <td>
                    <strong>Possui Alergias? (Esmalte, cola, etc)</strong>
                    {{ $record->has_allergy ? '☒ Sim' : '☐ Não' }}
                </td>
                <td>
                    <strong>Problemas Oculares? (Conjuntivite, etc)</strong>
                    {{ $record->eye_disease ? '☒ Sim' : '☐ Não' }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Grávida ou Lactante?</strong>
                    {{ $record->pregnant_or_lactating ? '☒ Sim' : '☐ Não' }}
                </td>
                <td>
                    <strong>Usa Lentes de Contato?</strong>
                    {{ $record->uses_contact_lenses ? '☒ Sim' : '☐ Não' }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Problemas de Tireoide?</strong>
                    {{ $record->thyroid_problem ? '☒ Sim' : '☐ Não' }}
                </td>
                <td>
                    <strong>Dorme de Bruços?</strong>
                    {{ $record->sleeps_on_stomach ? '☒ Sim' : '☐ Não' }}
                </td>
            </tr>
        </table>

        <div class="section-title">Detalhes do Procedimento</div>
        <table>
            <tr>
                <td class="label">Estilo Preferido:</td>
                <td class="value">{{ $record->preferred_style ?? '__________________________________' }}</td>
            </tr>
            <tr>
                <td class="label">Mapping Utilizado:</td>
                <td class="value">{{ $record->mapping_details ?? '__________________________________' }}</td>
            </tr>
            <tr>
                <td class="label">Observações Adicionais:</td>
                <td class="value">{{ $record->observations ?? 'Nenhuma observação.' }}</td>
            </tr>
        </table>

        <div class="signature-box">
            <p style="font-size: 11px; color: #666; margin-bottom: 20px;">
                Declaro que as informações acima são verdadeiras e estou ciente de todas as orientações <br> 
                e cuidados pós-procedimento necessários para a longevidade das extensões.
            </p>
            
            <div style="width: 350px; margin: 0 auto; text-align: center;">
                @if($record->signature)
                    <img src="{{ $record->signature }}" alt="Assinatura" style="max-height: 80px; display: block; margin: 0 auto;">
                @else
                    <br><br><br>
                @endif
            </div>
            
            <div class="signature-line">
                Assinatura da Cliente
            </div>
            <p style="margin-top: 5px;">{{ $record->created_at->format('d/m/Y') }}</p>
        </div>
        
        <div class="footer">
            Documento gerado digitalmente pelo sistema.
        </div>
    </div>

</body>

</html>