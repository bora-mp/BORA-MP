<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.bora.net/
 *
 *
*/

declare(strict_types=1);

namespace bora\network\mcpe;

use bora\network\mcpe\protocol\AddBehaviorTreePacket;
use bora\network\mcpe\protocol\AddEntityPacket;
use bora\network\mcpe\protocol\AddItemEntityPacket;
use bora\network\mcpe\protocol\AddPaintingPacket;
use bora\network\mcpe\protocol\AddPlayerPacket;
use bora\network\mcpe\protocol\AdventureSettingsPacket;
use bora\network\mcpe\protocol\AnimatePacket;
use bora\network\mcpe\protocol\AvailableCommandsPacket;
use bora\network\mcpe\protocol\AvailableEntityIdentifiersPacket;
use bora\network\mcpe\protocol\BiomeDefinitionListPacket;
use bora\network\mcpe\protocol\BlockEntityDataPacket;
use bora\network\mcpe\protocol\BlockEventPacket;
use bora\network\mcpe\protocol\BlockPickRequestPacket;
use bora\network\mcpe\protocol\BookEditPacket;
use bora\network\mcpe\protocol\BossEventPacket;
use bora\network\mcpe\protocol\CameraPacket;
use bora\network\mcpe\protocol\ChangeDimensionPacket;
use bora\network\mcpe\protocol\ChunkRadiusUpdatedPacket;
use bora\network\mcpe\protocol\ClientToServerHandshakePacket;
use bora\network\mcpe\protocol\ClientboundMapItemDataPacket;
use bora\network\mcpe\protocol\CommandBlockUpdatePacket;
use bora\network\mcpe\protocol\CommandOutputPacket;
use bora\network\mcpe\protocol\CommandRequestPacket;
use bora\network\mcpe\protocol\ContainerClosePacket;
use bora\network\mcpe\protocol\ContainerOpenPacket;
use bora\network\mcpe\protocol\ContainerSetDataPacket;
use bora\network\mcpe\protocol\CraftingDataPacket;
use bora\network\mcpe\protocol\CraftingEventPacket;
use bora\network\mcpe\protocol\DataPacket;
use bora\network\mcpe\protocol\DisconnectPacket;
use bora\network\mcpe\protocol\EntityEventPacket;
use bora\network\mcpe\protocol\EntityFallPacket;
use bora\network\mcpe\protocol\EntityPickRequestPacket;
use bora\network\mcpe\protocol\EventPacket;
use bora\network\mcpe\protocol\ExplodePacket;
use bora\network\mcpe\protocol\FullChunkDataPacket;
use bora\network\mcpe\protocol\GameRulesChangedPacket;
use bora\network\mcpe\protocol\GuiDataPickItemPacket;
use bora\network\mcpe\protocol\HurtArmorPacket;
use bora\network\mcpe\protocol\InteractPacket;
use bora\network\mcpe\protocol\InventoryContentPacket;
use bora\network\mcpe\protocol\InventorySlotPacket;
use bora\network\mcpe\protocol\InventoryTransactionPacket;
use bora\network\mcpe\protocol\ItemFrameDropItemPacket;
use bora\network\mcpe\protocol\LabTablePacket;
use bora\network\mcpe\protocol\LevelEventPacket;
use bora\network\mcpe\protocol\LevelSoundEventPacket;
use bora\network\mcpe\protocol\LevelSoundEventPacketV1;
use bora\network\mcpe\protocol\LevelSoundEventPacketV2;
use bora\network\mcpe\protocol\LoginPacket;
use bora\network\mcpe\protocol\MapInfoRequestPacket;
use bora\network\mcpe\protocol\MobArmorEquipmentPacket;
use bora\network\mcpe\protocol\MobEffectPacket;
use bora\network\mcpe\protocol\MobEquipmentPacket;
use bora\network\mcpe\protocol\ModalFormRequestPacket;
use bora\network\mcpe\protocol\ModalFormResponsePacket;
use bora\network\mcpe\protocol\MoveEntityAbsolutePacket;
use bora\network\mcpe\protocol\MoveEntityDeltaPacket;
use bora\network\mcpe\protocol\MovePlayerPacket;
use bora\network\mcpe\protocol\NetworkChunkPublisherUpdatePacket;
use bora\network\mcpe\protocol\NetworkStackLatencyPacket;
use bora\network\mcpe\protocol\NpcRequestPacket;
use bora\network\mcpe\protocol\PhotoTransferPacket;
use bora\network\mcpe\protocol\PlaySoundPacket;
use bora\network\mcpe\protocol\PlayStatusPacket;
use bora\network\mcpe\protocol\PlayerActionPacket;
use bora\network\mcpe\protocol\PlayerHotbarPacket;
use bora\network\mcpe\protocol\PlayerInputPacket;
use bora\network\mcpe\protocol\PlayerListPacket;
use bora\network\mcpe\protocol\PlayerSkinPacket;
use bora\network\mcpe\protocol\PurchaseReceiptPacket;
use bora\network\mcpe\protocol\RemoveEntityPacket;
use bora\network\mcpe\protocol\RemoveObjectivePacket;
use bora\network\mcpe\protocol\RequestChunkRadiusPacket;
use bora\network\mcpe\protocol\ResourcePackChunkDataPacket;
use bora\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use bora\network\mcpe\protocol\ResourcePackClientResponsePacket;
use bora\network\mcpe\protocol\ResourcePackDataInfoPacket;
use bora\network\mcpe\protocol\ResourcePackStackPacket;
use bora\network\mcpe\protocol\ResourcePacksInfoPacket;
use bora\network\mcpe\protocol\RespawnPacket;
use bora\network\mcpe\protocol\RiderJumpPacket;
use bora\network\mcpe\protocol\ScriptCustomEventPacket;
use bora\network\mcpe\protocol\ServerSettingsRequestPacket;
use bora\network\mcpe\protocol\ServerSettingsResponsePacket;
use bora\network\mcpe\protocol\ServerToClientHandshakePacket;
use bora\network\mcpe\protocol\SetCommandsEnabledPacket;
use bora\network\mcpe\protocol\SetDefaultGameTypePacket;
use bora\network\mcpe\protocol\SetDifficultyPacket;
use bora\network\mcpe\protocol\SetDisplayObjectivePacket;
use bora\network\mcpe\protocol\SetEntityDataPacket;
use bora\network\mcpe\protocol\SetEntityLinkPacket;
use bora\network\mcpe\protocol\SetEntityMotionPacket;
use bora\network\mcpe\protocol\SetHealthPacket;
use bora\network\mcpe\protocol\SetLastHurtByPacket;
use bora\network\mcpe\protocol\SetLocalPlayerAsInitializedPacket;
use bora\network\mcpe\protocol\SetPlayerGameTypePacket;
use bora\network\mcpe\protocol\SetScorePacket;
use bora\network\mcpe\protocol\SetScoreboardIdentityPacket;
use bora\network\mcpe\protocol\SetSpawnPositionPacket;
use bora\network\mcpe\protocol\SetTimePacket;
use bora\network\mcpe\protocol\SetTitlePacket;
use bora\network\mcpe\protocol\ShowCreditsPacket;
use bora\network\mcpe\protocol\ShowProfilePacket;
use bora\network\mcpe\protocol\ShowStoreOfferPacket;
use bora\network\mcpe\protocol\SimpleEventPacket;
use bora\network\mcpe\protocol\SpawnExperienceOrbPacket;
use bora\network\mcpe\protocol\SpawnParticleEffectPacket;
use bora\network\mcpe\protocol\StartGamePacket;
use bora\network\mcpe\protocol\StopSoundPacket;
use bora\network\mcpe\protocol\StructureBlockUpdatePacket;
use bora\network\mcpe\protocol\SubClientLoginPacket;
use bora\network\mcpe\protocol\TakeItemEntityPacket;
use bora\network\mcpe\protocol\TextPacket;
use bora\network\mcpe\protocol\TransferPacket;
use bora\network\mcpe\protocol\UpdateAttributesPacket;
use bora\network\mcpe\protocol\UpdateBlockPacket;
use bora\network\mcpe\protocol\UpdateBlockSyncedPacket;
use bora\network\mcpe\protocol\UpdateEquipPacket;
use bora\network\mcpe\protocol\UpdateSoftEnumPacket;
use bora\network\mcpe\protocol\UpdateTradePacket;
use bora\network\mcpe\protocol\WSConnectPacket;

abstract class NetworkSession{

	abstract public function handleDataPacket(DataPacket $packet);

	public function handleLogin(LoginPacket $packet) : bool{
		return false;
	}

	public function handlePlayStatus(PlayStatusPacket $packet) : bool{
		return false;
	}

	public function handleServerToClientHandshake(ServerToClientHandshakePacket $packet) : bool{
		return false;
	}

	public function handleClientToServerHandshake(ClientToServerHandshakePacket $packet) : bool{
		return false;
	}

	public function handleDisconnect(DisconnectPacket $packet) : bool{
		return false;
	}

	public function handleResourcePacksInfo(ResourcePacksInfoPacket $packet) : bool{
		return false;
	}

	public function handleResourcePackStack(ResourcePackStackPacket $packet) : bool{
		return false;
	}

	public function handleResourcePackClientResponse(ResourcePackClientResponsePacket $packet) : bool{
		return false;
	}

	public function handleText(TextPacket $packet) : bool{
		return false;
	}

	public function handleSetTime(SetTimePacket $packet) : bool{
		return false;
	}

	public function handleStartGame(StartGamePacket $packet) : bool{
		return false;
	}

	public function handleAddPlayer(AddPlayerPacket $packet) : bool{
		return false;
	}

	public function handleAddEntity(AddEntityPacket $packet) : bool{
		return false;
	}

	public function handleRemoveEntity(RemoveEntityPacket $packet) : bool{
		return false;
	}

	public function handleAddItemEntity(AddItemEntityPacket $packet) : bool{
		return false;
	}

	public function handleTakeItemEntity(TakeItemEntityPacket $packet) : bool{
		return false;
	}

	public function handleMoveEntityAbsolute(MoveEntityAbsolutePacket $packet) : bool{
		return false;
	}

	public function handleMovePlayer(MovePlayerPacket $packet) : bool{
		return false;
	}

	public function handleRiderJump(RiderJumpPacket $packet) : bool{
		return false;
	}

	public function handleUpdateBlock(UpdateBlockPacket $packet) : bool{
		return false;
	}

	public function handleAddPainting(AddPaintingPacket $packet) : bool{
		return false;
	}

	public function handleExplode(ExplodePacket $packet) : bool{
		return false;
	}

	public function handleLevelSoundEventPacketV1(LevelSoundEventPacketV1 $packet) : bool{
		return false;
	}

	public function handleLevelEvent(LevelEventPacket $packet) : bool{
		return false;
	}

	public function handleBlockEvent(BlockEventPacket $packet) : bool{
		return false;
	}

	public function handleEntityEvent(EntityEventPacket $packet) : bool{
		return false;
	}

	public function handleMobEffect(MobEffectPacket $packet) : bool{
		return false;
	}

	public function handleUpdateAttributes(UpdateAttributesPacket $packet) : bool{
		return false;
	}

	public function handleInventoryTransaction(InventoryTransactionPacket $packet) : bool{
		return false;
	}

	public function handleMobEquipment(MobEquipmentPacket $packet) : bool{
		return false;
	}

	public function handleMobArmorEquipment(MobArmorEquipmentPacket $packet) : bool{
		return false;
	}

	public function handleInteract(InteractPacket $packet) : bool{
		return false;
	}

	public function handleBlockPickRequest(BlockPickRequestPacket $packet) : bool{
		return false;
	}

	public function handleEntityPickRequest(EntityPickRequestPacket $packet) : bool{
		return false;
	}

	public function handlePlayerAction(PlayerActionPacket $packet) : bool{
		return false;
	}

	public function handleEntityFall(EntityFallPacket $packet) : bool{
		return false;
	}

	public function handleHurtArmor(HurtArmorPacket $packet) : bool{
		return false;
	}

	public function handleSetEntityData(SetEntityDataPacket $packet) : bool{
		return false;
	}

	public function handleSetEntityMotion(SetEntityMotionPacket $packet) : bool{
		return false;
	}

	public function handleSetEntityLink(SetEntityLinkPacket $packet) : bool{
		return false;
	}

	public function handleSetHealth(SetHealthPacket $packet) : bool{
		return false;
	}

	public function handleSetSpawnPosition(SetSpawnPositionPacket $packet) : bool{
		return false;
	}

	public function handleAnimate(AnimatePacket $packet) : bool{
		return false;
	}

	public function handleRespawn(RespawnPacket $packet) : bool{
		return false;
	}

	public function handleContainerOpen(ContainerOpenPacket $packet) : bool{
		return false;
	}

	public function handleContainerClose(ContainerClosePacket $packet) : bool{
		return false;
	}

	public function handlePlayerHotbar(PlayerHotbarPacket $packet) : bool{
		return false;
	}

	public function handleInventoryContent(InventoryContentPacket $packet) : bool{
		return false;
	}

	public function handleInventorySlot(InventorySlotPacket $packet) : bool{
		return false;
	}

	public function handleContainerSetData(ContainerSetDataPacket $packet) : bool{
		return false;
	}

	public function handleCraftingData(CraftingDataPacket $packet) : bool{
		return false;
	}

	public function handleCraftingEvent(CraftingEventPacket $packet) : bool{
		return false;
	}

	public function handleGuiDataPickItem(GuiDataPickItemPacket $packet) : bool{
		return false;
	}

	public function handleAdventureSettings(AdventureSettingsPacket $packet) : bool{
		return false;
	}

	public function handleBlockEntityData(BlockEntityDataPacket $packet) : bool{
		return false;
	}

	public function handlePlayerInput(PlayerInputPacket $packet) : bool{
		return false;
	}

	public function handleFullChunkData(FullChunkDataPacket $packet) : bool{
		return false;
	}

	public function handleSetCommandsEnabled(SetCommandsEnabledPacket $packet) : bool{
		return false;
	}

	public function handleSetDifficulty(SetDifficultyPacket $packet) : bool{
		return false;
	}

	public function handleChangeDimension(ChangeDimensionPacket $packet) : bool{
		return false;
	}

	public function handleSetPlayerGameType(SetPlayerGameTypePacket $packet) : bool{
		return false;
	}

	public function handlePlayerList(PlayerListPacket $packet) : bool{
		return false;
	}

	public function handleSimpleEvent(SimpleEventPacket $packet) : bool{
		return false;
	}

	public function handleEvent(EventPacket $packet) : bool{
		return false;
	}

	public function handleSpawnExperienceOrb(SpawnExperienceOrbPacket $packet) : bool{
		return false;
	}

	public function handleClientboundMapItemData(ClientboundMapItemDataPacket $packet) : bool{
		return false;
	}

	public function handleMapInfoRequest(MapInfoRequestPacket $packet) : bool{
		return false;
	}

	public function handleRequestChunkRadius(RequestChunkRadiusPacket $packet) : bool{
		return false;
	}

	public function handleChunkRadiusUpdated(ChunkRadiusUpdatedPacket $packet) : bool{
		return false;
	}

	public function handleItemFrameDropItem(ItemFrameDropItemPacket $packet) : bool{
		return false;
	}

	public function handleGameRulesChanged(GameRulesChangedPacket $packet) : bool{
		return false;
	}

	public function handleCamera(CameraPacket $packet) : bool{
		return false;
	}

	public function handleBossEvent(BossEventPacket $packet) : bool{
		return false;
	}

	public function handleShowCredits(ShowCreditsPacket $packet) : bool{
		return false;
	}

	public function handleAvailableCommands(AvailableCommandsPacket $packet) : bool{
		return false;
	}

	public function handleCommandRequest(CommandRequestPacket $packet) : bool{
		return false;
	}

	public function handleCommandBlockUpdate(CommandBlockUpdatePacket $packet) : bool{
		return false;
	}

	public function handleCommandOutput(CommandOutputPacket $packet) : bool{
		return false;
	}

	public function handleUpdateTrade(UpdateTradePacket $packet) : bool{
		return false;
	}

	public function handleUpdateEquip(UpdateEquipPacket $packet) : bool{
		return false;
	}

	public function handleResourcePackDataInfo(ResourcePackDataInfoPacket $packet) : bool{
		return false;
	}

	public function handleResourcePackChunkData(ResourcePackChunkDataPacket $packet) : bool{
		return false;
	}

	public function handleResourcePackChunkRequest(ResourcePackChunkRequestPacket $packet) : bool{
		return false;
	}

	public function handleTransfer(TransferPacket $packet) : bool{
		return false;
	}

	public function handlePlaySound(PlaySoundPacket $packet) : bool{
		return false;
	}

	public function handleStopSound(StopSoundPacket $packet) : bool{
		return false;
	}

	public function handleSetTitle(SetTitlePacket $packet) : bool{
		return false;
	}

	public function handleAddBehaviorTree(AddBehaviorTreePacket $packet) : bool{
		return false;
	}

	public function handleStructureBlockUpdate(StructureBlockUpdatePacket $packet) : bool{
		return false;
	}

	public function handleShowStoreOffer(ShowStoreOfferPacket $packet) : bool{
		return false;
	}

	public function handlePurchaseReceipt(PurchaseReceiptPacket $packet) : bool{
		return false;
	}

	public function handlePlayerSkin(PlayerSkinPacket $packet) : bool{
		return false;
	}

	public function handleSubClientLogin(SubClientLoginPacket $packet) : bool{
		return false;
	}

	public function handleWSConnect(WSConnectPacket $packet) : bool{
		return false;
	}

	public function handleSetLastHurtBy(SetLastHurtByPacket $packet) : bool{
		return false;
	}

	public function handleBookEdit(BookEditPacket $packet) : bool{
		return false;
	}

	public function handleNpcRequest(NpcRequestPacket $packet) : bool{
		return false;
	}

	public function handlePhotoTransfer(PhotoTransferPacket $packet) : bool{
		return false;
	}

	public function handleModalFormRequest(ModalFormRequestPacket $packet) : bool{
		return false;
	}

	public function handleModalFormResponse(ModalFormResponsePacket $packet) : bool{
		return false;
	}

	public function handleServerSettingsRequest(ServerSettingsRequestPacket $packet) : bool{
		return false;
	}

	public function handleServerSettingsResponse(ServerSettingsResponsePacket $packet) : bool{
		return false;
	}

	public function handleShowProfile(ShowProfilePacket $packet) : bool{
		return false;
	}

	public function handleSetDefaultGameType(SetDefaultGameTypePacket $packet) : bool{
		return false;
	}

	public function handleRemoveObjective(RemoveObjectivePacket $packet) : bool{
		return false;
	}

	public function handleSetDisplayObjective(SetDisplayObjectivePacket $packet) : bool{
		return false;
	}

	public function handleSetScore(SetScorePacket $packet) : bool{
		return false;
	}

	public function handleLabTable(LabTablePacket $packet) : bool{
		return false;
	}

	public function handleUpdateBlockSynced(UpdateBlockSyncedPacket $packet) : bool{
		return false;
	}

	public function handleMoveEntityDelta(MoveEntityDeltaPacket $packet) : bool{
		return false;
	}

	public function handleSetScoreboardIdentity(SetScoreboardIdentityPacket $packet) : bool{
		return false;
	}

	public function handleSetLocalPlayerAsInitialized(SetLocalPlayerAsInitializedPacket $packet) : bool{
		return false;
	}

	public function handleUpdateSoftEnum(UpdateSoftEnumPacket $packet) : bool{
		return false;
	}

	public function handleNetworkStackLatency(NetworkStackLatencyPacket $packet) : bool{
		return false;
	}

	public function handleScriptCustomEvent(ScriptCustomEventPacket $packet) : bool{
		return false;
	}

	public function handleSpawnParticleEffect(SpawnParticleEffectPacket $packet) : bool{
		return false;
	}

	public function handleAvailableEntityIdentifiers(AvailableEntityIdentifiersPacket $packet) : bool{
		return false;
	}

	public function handleLevelSoundEventPacketV2(LevelSoundEventPacketV2 $packet) : bool{
		return false;
	}

	public function handleNetworkChunkPublisherUpdate(NetworkChunkPublisherUpdatePacket $packet) : bool{
		return false;
	}

	public function handleBiomeDefinitionList(BiomeDefinitionListPacket $packet) : bool{
		return false;
	}

	public function handleLevelSoundEvent(LevelSoundEventPacket $packet) : bool{
		return false;
	}

}
