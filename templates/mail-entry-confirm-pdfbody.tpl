{if

$area == 'Vitenfabrikken' or $area == 'Flyhistorisk museum Sola' or $area == 'Rogaland Krigshistoriske Museum'}

Bestillingsbekreftelse - {$area}

Takk for bestillingen! Her kommer bekreftelse med relevante opplysninger{if $invoice == "1"} og priser{/if}.

Vi ser fram til bes�ket og gleder oss til � gi dere en ny opplevelse.

Ta kontakt hvis noen av opplysningene ikke stemmer i det vedlagte dokumentet.

--
Med vennlig hilsen
{$user_name}
{if $user_position|strlen > 0}{$user_position}, {/if}J�rmuseet

E-post: {$user_email}

{if $user_phone|strlen > 0}Telefon: {$user_phone}{/if}

{else


}

Stadfesting av bestilling - {$area}

Takk for bestillinga! Her kjem stadfesting med relevante opplysingar{if $invoice == "1"} og prisar{/if}.

Me ser fram til bes�ket og gler oss til � gje dykk ei gild oppleving.

Ta kontakt viss nokre av opplysingane ikkje stemmer i dokumentet som er lagt ved.

--
Beste helsing
{$user_name}
{if $user_position|strlen > 0}{$user_position}, {/if}J�rmuseet

E-post: {$user_email}
{if $user_phone|strlen > 0}Telefon: {$user_phone}{/if}

{/if}