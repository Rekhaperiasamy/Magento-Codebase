<?php

namespace Orange\Catalog\Setup;

use Magento\Cms\Model\Block;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\UpgradeDataInterface; 
use Magento\Framework\Setup\ModuleContextInterface; 
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
	private $blockFactory;

	public function __construct(BlockFactory $blockFactory)
	{
		$this->blockFactory = $blockFactory;
	}

   /* const TABLE_CMS_BLOCK = 'cms_block';

    const TABLE_CMS_BLOCK_STORE = 'cms_block_store';

    const STORE_ID = 0;*/
	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
		if (version_compare($context->getVersion(), '1.0.2') < 0) {
			$buyonline = [
				'title' => 'Porquoi acheter en ligne',
				'identifier' => 'buy-online',
				'stores' => [0],
				'content' => "<div class='field-item even'>
							<a class='sticky-a' name='4-raisons-de-commander-en-ligne'>
							</a>
							<article id='node-63491' class='node node-layer-columns layer-style--gray clearfix'>
								<div class='container'>
									<div class='field field-name-field-display-title field-type-text field-label-hidden'>
										<div class='field-items'>
											<div class='field-item even'>
												<h2>
													Porquoi acheter en ligne
												</h2>
											</div>
										</div>
									</div>
									<div class='field-collection-container clearfix'>
										<div class='field field-name-field-column field-type-field-collection field-label-hidden'>
											<div class='field-items'>
												<div class='field-item even'>
													<div class='field-collection-view clearfix view-mode-full col-xs-12 col-xs-push-inherit col-sm-3 col-sm-push-inherit col-md-inherit col-md-push-inherit'>
														<div class='entity entity-field-collection-item field-collection-item-field-column icon-above style-col-default clearfix'>
															<div class='content'>
																<div class='field field-name-field-icon field-type-list-text field-label-hidden'>
																	<div class='field-items'>
																		<div class='field-item even'>
																			<i class='oi oi-usp'>
																			</i>
																		</div>
																	</div>
																</div>
																<div class='field field-name-field-column-body field-type-text-long field-label-hidden'>
																	<div class='field-items'>
																		<div class='field-item even'>
																			<h4>
																				USP 1 
																			</h4>
																			<p>Lorem ipsum <br>et doloris con quiat</p>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class='field-item odd'>
													<div class='field-collection-view clearfix view-mode-full col-xs-12 col-xs-push-inherit col-sm-3 col-sm-push-inherit col-md-inherit col-md-push-inherit'>
														<div class='entity entity-field-collection-item field-collection-item-field-column icon-above style-col-default clearfix'>
															<div class='content'>
																<div class='field field-name-field-icon field-type-list-text field-label-hidden'>
																	<div class='field-items'>
																		<div class='field-item even'>
																			<i class='oi oi-signal'>
																			</i>
																		</div>
																	</div>
																</div>
																<div class='field field-name-field-column-body field-type-text-long field-label-hidden'>
																	<div class='field-items'>
																		<div class='field-item even'>
																			<h4>
																				USP 2 
																			</h4>
																			<p>Lorem ipsum <br>et doloris con quiat</p>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class='field-item even'>
													<div class='field-collection-view clearfix view-mode-full col-xs-12 col-xs-push-inherit col-sm-3 col-sm-push-inherit col-md-inherit col-md-push-inherit'>
														<div class='entity entity-field-collection-item field-collection-item-field-column icon-above style-col-default clearfix'>
															<div class='content'>
																<div class='field field-name-field-icon field-type-list-text field-label-hidden'>
																	<div class='field-items'>
																		<div class='field-item even'>
																			<i class='oi oi-tablet_smartphone'>
																			</i>
																		</div>
																	</div>
																</div>
																<div class='field field-name-field-column-body field-type-text-long field-label-hidden'>
																	<div class='field-items'>
																		<div class='field-item even'>
																			<h4>
																				USP 3 
																			</h4>
																			<p>Lorem ipsum <br>et doloris con quiat</p>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class='field-item odd'>
													<div class='field-collection-view clearfix view-mode-full field-collection-view-final col-xs-12 col-xs-push-inherit col-sm-3 col-sm-push-inherit col-md-inherit col-md-push-inherit'>
														<div class='entity entity-field-collection-item field-collection-item-field-column icon-above style-col-default clearfix'>
															<div class='content'>
																<div class='field field-name-field-icon field-type-list-text field-label-hidden'>
																	<div class='field-items'>
																		<div class='field-item even'>
																			<i class='oi oi-signals'>
																			</i>
																		</div>
																	</div>
																</div>
																<div class='field field-name-field-column-body field-type-text-long field-label-hidden'>
																	<div class='field-items'>
																		<div class='field-item even'>
																			<h4>
																				USP 4 
																			</h4>
																			<p>Lorem ipsum <br>et doloris con quiat</p>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<footer>
									</footer>
								</div>
							</article>
						</div>",
				'is_active' => true
			];
			$stepbystep = [
				'title' => 'Etapes par étapes',
				'identifier' => 'step-by-step',
				'stores' => [0],
				'content' => "<div class='container'>
					<div class='row'>
						<div class='col-sm-12 content-collapsable margin-xs-b-l'>
							<h2>
								Etapes par étapes
							</h2>

							<h4 class='padding-xs-r-m'>
								Comment se passe la livraison de votre smartphone combiné à un abonnement&nbsp;?
							</h4>
							<ol class='closed'>
								<li>
									<strong>
										Recevez votre carte SIM
									</strong>
									<br />
									Si vous êtes un nouveau client, vous recevrez tout d’abord une nouvelle carte SIM endéans les 2 jours ouvrables. Pour des raisons de sécurité, la carte SIM ne sera pas activée.
									<br />
									Remarque : Si vous utilisez une carte prépayée Orange, vous n’avez pas besoin de changer de carte SIM. Votre abonnement sera automatiquement activé en 48 heures et votre smartphone sera ensuite livré endéans les 2 jours ouvrables.
								</li>
								<li>
									<strong>
										Activez votre carte SIM
									</strong>
									<br />
									Pour que votre abonnement soit actif, activez la carte SIM reçue sur www.orange.be/activer. 
									<br />
									Votre carte SIM sera prête à l’emploi endéans les 24 heures.
									<p class='margin-xs-t-s'>
										Votre carte SIM est compatible avec tous les téléphones (normal / micro / nano SIM) !
									</p>
								</li>
								<li>
									<strong>
										Recevez un smartphone
									</strong>
									<br />
									Une fois que votre abonnement est actif, votre GSM vous sera automatiquement envoyé endéans les 2 jours ouvrables.
									<p class='margin-xs-t-s'>
										Vous avez déjà un abonnement ?
									<br />
										Rendez-vous dans votre point de vente Orange pour profiter de ces offres !
									</p>
								</li>
							</ol>

							<h4 class='padding-xs-r-m'>
								Vous n&#039;êtes pas encore un client Orange&nbsp;?
							</h4>
							<ol class='closed'>
								<li>
									<strong>
										Commandez votre abonnement en ligne
									</strong>
									<br /> Choisissez l'abonnement que vous souhaitez sur notre site web et suivez simplement les étapes. Pas d'inquiétude, le système vous guidera sur la marche à suivre.
								</li>
								<li>
									<strong>
										Recevez et activez votre carte SIM
									</strong>
									<br /> Votre nouvelle carte SIM vous sera livrée endéans les 2 jours ouvrables via bpost. Attention, par sécurité, votre carte SIM n'est pas encore activée.<br /> Pour ce faire, suivez les instructions que vous retrouverez dans l'enveloppe contenant votre carte SIM. Votre carte SIM est prête à l'utilisation dans les 24 heures.<br /> Votre carte SIM est compatible avec tous les appareils (normale / micro / nano SIM).
								</li>
							</ol>

							<h4 class='padding-xs-r-m'>
								Vous avez une carte prépayée&nbsp;?
							</h4>
							<ol class='closed'>
								<li>
									<strong>
										Commandez votre abonnement en ligne
									</strong>
									<br /> Choisissez l'abonnement que vous souhaitez sur notre site web et suivez simplement les étapes. Pas d'inquiétude, le système vous guidera sur la marche à suivre.
								</li>
								<li>
									<strong>
										Nous nous occupons du reste
									</strong>
									<br /> A la fin de votre commande en ligne, vous recevrez un mail de confirmation. Ensuite, votre abonnement sera automatiquement activé en 24 heures. Nous vous en informerons par SMS.<br /> Vous conservez votre numéro, votre carte SIM et votre crédit d’appel restant.
								</li>
							</ol>

							<h4 class='padding-xs-r-m'>
								Vous avez un autre plan tarifaire&nbsp;?
							</h4>
							<ol class='closed'>
								<li>
									<strong>
										Rendez-vous dans votre Espace client ou dans un shop Orange
									</strong>
									<br /> Pour passer à un abonnement avec smartphone, deux possibilités s'offrent à vous : 
									<ul>
										<li>
											Vous pouvez très facilement et rapidement changer votre abonnement vous-même dans votre 
											<a href='https://www.orange.be/fr/e-services/login'>
												Espace client
											</a>
											.<br /> Connectez-vous, choisissez 'gérer mon abonnement' et personnalisez votre demande en quelques clics.
										</li>
										<li>
											Vous pouvez également modifier votre abonnement dans le 
											<a href='http://shops.orange.be/b2c/?lang=fr'>
												shop Orange
											</a>
											le plus proche.
										</li>
									</ul>
								</li>
								<li>
									<strong>
										Bon à savoir
									</strong>
									<br /> Si vous avez déjà un abonnement avec smartphone, 
									<a href='/node/56621'>
										cliquez ici
									</a>
									pour toutes les informations sur le changement de plan tarifaire selon la durée de votre contrat.
								</li>
								<li>
									<strong>
										Besoin d'aide? 
									</strong>
									<br /> Contactez-nous au 5000 depuis votre GSM Orange. Un de nos collaborateurs vous aidera à choisir le plan tarifaire le plus adapté à votre consommation.
								</li>
							</ol>
							<p></p>
						</div>
					</div>
				</div>",
				'is_active' => true
			];
			$this->blockFactory->create()->setData($buyonline)->save();
			$this->blockFactory->create()->setData($stepbystep)->save();
		}
		if (version_compare($context->getVersion(), '1.0.3') < 0) {
			$devicestepbystep = [
				'title' => 'Etapes par étapes',
				'identifier' => 'device-step-by-step',
				'stores' => [0],
				'content' => '<div class="row padding-xs-b-l">
					<div class="col-xs-12">
						<div class="content-collapsable">
							<h2>Etapes par étapes</h2>
							<h4 class="padding-xs-r-m">
								Standalone purchase
							</h4>
							<div class="closed">
								<p>Vous avez commandé un smartphone sans abonnement:</p>
								<p class="font-normal">Toute commande passée avant 19h vous sera livrée gratuitement le lendemain par bpost, même le samedi. Seules les commandes effectuées le week-end (samedi et dimanche), ne vous parviendront que le mardi suivant.<br><br></p>

							</div>
							<h4 class="padding-xs-r-m">
								Subsidy purchase
							</h4>
							<div class="closed">
								<p>Si vous êtes un nouveau client Orange :</p>
								<ol>
									<li>
										<strong>
											Livraison de votre carte SIM
										</strong>
										<br>
										Vous recevrez votre nouvelle carte SIM par bpost dans les 2 jours ouvrables. Pour des raisons de sécurité, votre carte SIM n’est pas encore activée.
									</li>
									<li>
										<strong>
											Activation de votre carte SIM
										</strong>
										<br>
										Pour activer votre carte SIM, rendez-vous sur <a href="http://www.orange.be/activer" target="_blank">www.orange.be/activer</a> et suivez les instructions d’activation. Votre carte SIM sera prête à l’emploi dans les 24 heures.
									</li>
									<li>
										<strong>
											Livraison de votre smartphone
										</strong>
										<br>
										Une fois votre carte SIM activée, votre GSM vous sera livré dans les 2 jours ouvrables. Vous recevrez un mail qui vous confirmera l’envoi de votre paquet.
									</li>
								</ol>
								<p>Si vous êtes déjà client Orange :</p>
								<p class="font-normal">Vous n’avez pas besoin de changer de carte SIM. Votre abonnement sera automatiquement activé en 24 heures et votre smartphone vous sera ensuite livré dans les 2 jours ouvrables.<br><br></p>								
							</div>
						</div>
					</div>
				</div>',
				'is_active' => true
			];
			$cashback = [
				'title' => 'Cashback',
				'identifier' => 'cash-back',
				'stores' => [0],
				'content' => '<div class="well-grey margin-xs-t-s margin-xs-t-m">
								<h5 class="font-24">Cashback de 50€</h5>
								<p>Action cashback en combinaison avec un abonnement Panthère Smartphone: 50 € remboursés après achat.</p>
								<a class="btn btn-default margin-xs-v-s" href="">Demandez votre cashback ici</a>
							</div>',
				'is_active' => true
			];
			$this->blockFactory->create()->setData($devicestepbystep)->save();
			$this->blockFactory->create()->setData($cashback)->save();
		}
	}
}