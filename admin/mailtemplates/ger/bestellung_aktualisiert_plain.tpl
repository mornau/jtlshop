{includeMailTemplate template=header type=plain}

Guten Tag {$Kunde->cVorname} {$Kunde->cNachname},

Ihre Bestellung bei {$Einstellungen.global.global_shopname} wurde aktualisiert.

Ihre Bestellung mit Bestellnummer {$Bestellung->cBestellNr} umfasst folgende Positionen:

{foreach $Bestellung->Positionen as $Position}

    {if $Position->nPosTyp == 1}
        {$Position->nAnzahl}x {$Position->cName} - {$Position->cGesamtpreisLocalized[$NettoPreise]}{if $Einstellungen.kaufabwicklung.bestellvorgang_lieferstatus_anzeigen === 'Y' && $Position->cLieferstatus}

        Lieferzeit: {$Position->cLieferstatus}{/if}
        {foreach $Position->WarenkorbPosEigenschaftArr as $WKPosEigenschaft}

            {$WKPosEigenschaft->cEigenschaftName}: {$WKPosEigenschaft->cEigenschaftWertName}{/foreach}
        {if $Position->cSeriennummer|strlen > 0}
            Seriennummer: {$Position->cSeriennummer}
        {/if}
        {if $Position->dMHD|strlen > 0}
            Mindesthaltbarkeitsdatum: {$Position->dMHD_de}
        {/if}
        {if $Position->cChargeNr|strlen > 0}
            Charge: {$Position->cChargeNr}
        {/if}
    {else}
        {$Position->nAnzahl}x {$Position->cName} - {$Position->cGesamtpreisLocalized[$NettoPreise]}{/if}
{/foreach}

{if $Einstellungen.global.global_steuerpos_anzeigen !== 'N'}{foreach $Bestellung->Steuerpositionen as $Steuerposition}
    {$Steuerposition->cName}: {$Steuerposition->cPreisLocalized}
{/foreach}{/if}
{if isset($Bestellung->GuthabenNutzen) && $Bestellung->GuthabenNutzen == 1}
    Gutschein: -{$Bestellung->GutscheinLocalized}
{/if}

Gesamtsumme: {$Bestellung->WarensummeLocalized[0]}


Ihre Rechnungsadresse:

{$Bestellung->oRechnungsadresse->cVorname} {$Bestellung->oRechnungsadresse->cNachname}
{$Bestellung->oRechnungsadresse->cStrasse} {$Bestellung->oRechnungsadresse->cHausnummer}
{if $Bestellung->oRechnungsadresse->cAdressZusatz}{$Bestellung->oRechnungsadresse->cAdressZusatz}{/if}
{$Bestellung->oRechnungsadresse->cPLZ} {$Bestellung->oRechnungsadresse->cOrt}
{if $Bestellung->oRechnungsadresse->cBundesland}{$Bestellung->oRechnungsadresse->cBundesland}{/if}
{if $Bestellung->oRechnungsadresse->cTel}Phone: {substr($Bestellung->oRechnungsadresse->cTel, 0, 2)}****{substr($Bestellung->oRechnungsadresse->cTel, -4)}
{/if}{if $Bestellung->oRechnungsadresse->cMobil}Mobile: {substr($Bestellung->oRechnungsadresse->cMobil, 0, 2)}****{substr($Bestellung->oRechnungsadresse->cMobil, -4)}
{/if}{if $Kunde->cFax}Fax: {$Kunde->cFax}
{/if}
Email: {$Bestellung->oRechnungsadresse->cMail}
{if $Kunde->cUSTID}Ust-ID: {$Kunde->cUSTID}
{/if}

{if $Bestellung->Lieferadresse->kLieferadresse>0}
    Ihre Lieferadresse:

    {$Bestellung->Lieferadresse->cVorname} {$Bestellung->Lieferadresse->cNachname}
    {$Bestellung->Lieferadresse->cStrasse} {$Bestellung->Lieferadresse->cHausnummer}
    {if $Bestellung->Lieferadresse->cAdressZusatz}{$Bestellung->Lieferadresse->cAdressZusatz}
    {/if}{$Bestellung->Lieferadresse->cPLZ} {$Bestellung->Lieferadresse->cOrt}
    {if $Bestellung->Lieferadresse->cBundesland}{$Bestellung->Lieferadresse->cBundesland}
    {/if}{$Bestellung->Lieferadresse->angezeigtesLand}
    {if $Bestellung->Lieferadresse->cTel}Tel.: {substr($Bestellung->Lieferadresse->cTel, 0, 2)}****{substr($Bestellung->Lieferadresse->cTel, -4)}
    {/if}{if $Bestellung->Lieferadresse->cMobil}Mobil: {substr($Bestellung->Lieferadresse->cMobil, 0, 2)}****{substr($Bestellung->Lieferadresse->cMobil, -4)}
{/if}{if $Bestellung->Lieferadresse->cFax}Fax: {$Bestellung->Lieferadresse->cFax}
{/if}{if $Bestellung->Lieferadresse->cMail}E-Mail: {$Bestellung->Lieferadresse->cMail}
{/if}
{else}
    Lieferadresse ist gleich Rechnungsadresse.
{/if}

Sie haben folgende Zahlungsart gewählt: {$Bestellung->cZahlungsartName}

{if isset($Zahlungsart->cHinweisText) && $Zahlungsart->cHinweisText|strlen > 0}  {$Zahlungsart->cHinweisText}


{/if}

{if $Bestellung->Zahlungsart->cModulId === 'za_rechnung_jtl'}
{elseif $Bestellung->Zahlungsart->cModulId === 'za_lastschrift_jtl'}
    Wir belasten in Kürze folgendes Bankkonto mit der fälligen Summe:

    Kontoinhaber: {$Bestellung->Zahlungsinfo->cInhaber}
    IBAN:  ****{substr($Bestellung->Zahlungsinfo->cIBAN, -4)}
    BIC: {$Bestellung->Zahlungsinfo->cBIC}
    Bank: {$Bestellung->Zahlungsinfo->cBankName}

{elseif $Bestellung->Zahlungsart->cModulId === 'za_barzahlung_jtl'}
{elseif $Bestellung->Zahlungsart->cModulId === 'za_paypal_jtl'}
    Falls Sie Ihre Zahlung per PayPal noch nicht durchgeführt haben, nutzen Sie folgende E-Mail-Adresse als Empfänger: {$Einstellungen.zahlungsarten.zahlungsart_paypal_empfaengermail}
{/if}

Über den weiteren Verlauf Ihrer Bestellung werden wir Sie jeweils gesondert informieren.


Mit freundlichem Gruß
Ihr Team von {$Firma->cName}

{includeMailTemplate template=footer type=plain}
