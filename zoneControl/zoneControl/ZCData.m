//
//  ZCData.m
//  zoneControl
//
//  Created by Brandon Jue on 6/18/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import "ZCData.h"
#import "cocos2d.h"

@implementation ZCData
BJ_SINGLETON_IMPLEMENTATION(ZCData, shared);

- (id)init {
	self = [super init];
	if (self) {
		self.units = [NSMutableDictionary dictionary];
		NSDictionary *lancer = @{@"type":@(ZCUnitTypeKnightLance),
								 @"name":@"Lancer",
								 @"health":@(80),
								 @"attack":@(5),
								 @"sprite":@[@[@"unit-body.png",@NO],
											 @[@"unit-pants.png",@YES],
											 @[@"unit-shirt.png",@YES],
											 @[@"unit-knight-hat.png",@YES],
											 @[@"unit-boot.png",@NO],
											 @[@"unit-lance.png",@NO],
											 @[@"unit-arm.png",@NO],
//											 @[@"unit-wizard-sleave.png",@YES],
											 @[@"unit-knight-gloves.png",@YES]
											 ],
								 @"movePattern":@[[NSValue valueWithCGPoint:ccp(-1, -1)],
												  [NSValue valueWithCGPoint:ccp(-1, 0)],
												  [NSValue valueWithCGPoint:ccp(-1, 1)],
												  [NSValue valueWithCGPoint:ccp(0, -1)],
												  [NSValue valueWithCGPoint:ccp(0, 1)],
												  [NSValue valueWithCGPoint:ccp(1, -1)],
												  [NSValue valueWithCGPoint:ccp(1, 0)],
												  [NSValue valueWithCGPoint:ccp(1, 1)]],
								 @"attackPattern":@[[NSValue valueWithCGPoint:ccp(0, 1)],
													[NSValue valueWithCGPoint:ccp(0, -1)],
													[NSValue valueWithCGPoint:ccp(1, 0)],
													[NSValue valueWithCGPoint:ccp(-1, 0)],
													[NSValue valueWithCGPoint:ccp(1, 1)],
													[NSValue valueWithCGPoint:ccp(1, -1)],
													[NSValue valueWithCGPoint:ccp(-1, 1)],
													[NSValue valueWithCGPoint:ccp(-1, -1)]]};
		[self.units setObject:lancer forKey:@(ZCUnitTypeKnightLance).description];
		NSDictionary *barbarian = @{@"type":@(ZCUnitTypeBarbarian),
									@"name":@"Barbarian",
									@"health":@(60),
									@"attack":@(10),
									@"sprite":@[@[@"unit-body.png",@NO],
												@[@"unit-pants.png",@YES],
//												@[@"unit-shirt.png",@YES],
//												@[@"unit-hat.png",@YES],
//												@[@"unit-boot.png",@NO],
//												@[@"unit-lance.png",@NO],
												@[@"unit-arm.png",@NO],
//												@[@"unit-sleave.png",@YES],
//												@[@"unit-gloves.png",@YES]
												],
									@"movePattern":@[[NSValue valueWithCGPoint:ccp(-1, -1)],
													 [NSValue valueWithCGPoint:ccp(-1, 0)],
													 [NSValue valueWithCGPoint:ccp(-1, 1)],
													 [NSValue valueWithCGPoint:ccp(0, -1)],
													 [NSValue valueWithCGPoint:ccp(0, 1)],
													 [NSValue valueWithCGPoint:ccp(1, -1)],
													 [NSValue valueWithCGPoint:ccp(1, 0)],
													 [NSValue valueWithCGPoint:ccp(1, 1)]],
									@"attackPattern":@[[NSValue valueWithCGPoint:ccp(0, 1)],
													   [NSValue valueWithCGPoint:ccp(0, -1)],
													   [NSValue valueWithCGPoint:ccp(1, 0)],
													   [NSValue valueWithCGPoint:ccp(-1, 0)]]};
		[self.units setObject:barbarian forKey:@(ZCUnitTypeBarbarian).description];
		NSDictionary *archer = @{@"type":@(ZCUnitTypeArcher),
								 @"name":@"Archer",
								 @"health":@(40),
								 @"attack":@(10),
								 @"sprite":@[@[@"unit-body.png",@NO],
											 @[@"unit-pants.png",@YES],
											 @[@"unit-shirt.png",@YES],
											 @[@"unit-archer-hat.png",@YES],
											 @[@"unit-boot.png",@NO],
											 @[@"unit-bow.png",@NO],
											 @[@"unit-arm.png",@NO],
											 @[@"unit-archer-sleave.png",@YES],
//											 @[@"unit-gloves.png",@YES]
											 ],
								 @"movePattern":@[[NSValue valueWithCGPoint:ccp(-1, -1)],
												  [NSValue valueWithCGPoint:ccp(-1, 0)],
												  [NSValue valueWithCGPoint:ccp(-1, 1)],
												  [NSValue valueWithCGPoint:ccp(0, -1)],
												  [NSValue valueWithCGPoint:ccp(0, 1)],
												  [NSValue valueWithCGPoint:ccp(1, -1)],
												  [NSValue valueWithCGPoint:ccp(1, 0)],
												  [NSValue valueWithCGPoint:ccp(1, 1)],
												  [NSValue valueWithCGPoint:ccp(-2, -2)],
												  [NSValue valueWithCGPoint:ccp(-2, 0)],
												  [NSValue valueWithCGPoint:ccp(-2, 2)],
												  [NSValue valueWithCGPoint:ccp(0, -2)],
												  [NSValue valueWithCGPoint:ccp(0, 2)],
												  [NSValue valueWithCGPoint:ccp(2, -2)],
												  [NSValue valueWithCGPoint:ccp(2, 0)],
												  [NSValue valueWithCGPoint:ccp(2, 2)]],
								 @"attackPattern":@[[NSValue valueWithCGPoint:ccp(0, 1)],
													[NSValue valueWithCGPoint:ccp(0, -1)],
													[NSValue valueWithCGPoint:ccp(1, 0)],
													[NSValue valueWithCGPoint:ccp(-1, 0)],
													[NSValue valueWithCGPoint:ccp(0, -2)],
													[NSValue valueWithCGPoint:ccp(2, 0)],
													[NSValue valueWithCGPoint:ccp(0, 2)],
													[NSValue valueWithCGPoint:ccp(-2, 0)],
													[NSValue valueWithCGPoint:ccp(0, -3)],
													[NSValue valueWithCGPoint:ccp(3, 0)],
													[NSValue valueWithCGPoint:ccp(0, 3)],
													[NSValue valueWithCGPoint:ccp(-3, 0)]]};
		[self.units setObject:archer forKey:@(ZCUnitTypeArcher).description];
		NSDictionary *knight = @{@"type":@(ZCUnitTypeKnightSword),
								 @"name":@"Knight",
								 @"health":@(120),
								 @"attack":@(20),
								 @"sprite":@[@[@"unit-body.png",@NO],
											 @[@"unit-pants.png",@YES],
											 @[@"unit-shirt.png",@YES],
											 @[@"unit-knight-hat.png",@YES],
											 @[@"unit-boot.png",@NO],
											 @[@"unit-sword.png",@NO],
											 @[@"unit-arm.png",@NO],
//											 @[@"unit-sleave.png",@YES],
											 @[@"unit-knight-gloves.png",@YES]
											 ],
								 @"movePattern":@[[NSValue valueWithCGPoint:ccp(-1, -1)],
												  [NSValue valueWithCGPoint:ccp(-1, 0)],
												  [NSValue valueWithCGPoint:ccp(-1, 1)],
												  [NSValue valueWithCGPoint:ccp(0, -1)],
												  [NSValue valueWithCGPoint:ccp(0, 1)],
												  [NSValue valueWithCGPoint:ccp(1, -1)],
												  [NSValue valueWithCGPoint:ccp(1, 0)],
												  [NSValue valueWithCGPoint:ccp(1, 1)]],
								 @"attackPattern":@[[NSValue valueWithCGPoint:ccp(0, 1)],
													[NSValue valueWithCGPoint:ccp(0, -1)],
													[NSValue valueWithCGPoint:ccp(1, 0)],
													[NSValue valueWithCGPoint:ccp(-1, 0)]]};
		[self.units setObject:knight forKey:@(ZCUnitTypeKnightSword).description];
		NSDictionary *wizard = @{@"type":@(ZCUnitTypeWizard),
								 @"name":@"Wizard",
								 @"health":@(40),
								 @"attack":@(40),
								 @"sprite":@[@[@"unit-body.png",@NO],
//											 @[@"unit-pants.png",@YES],
											 @[@"unit-wizard-shirt.png",@YES],
											 @[@"unit-wizard-hat.png",@YES],
											 @[@"unit-boot.png",@NO],
											 @[@"unit-wand.png",@NO],
											 @[@"unit-arm.png",@NO],
											 @[@"unit-wizard-sleave.png",@YES],
//											 @[@"unit-gloves.png",@YES]
											 ],
								 @"movePattern":@[[NSValue valueWithCGPoint:ccp(-1, -1)],
												  [NSValue valueWithCGPoint:ccp(-1, 0)],
												  [NSValue valueWithCGPoint:ccp(-1, 1)],
												  [NSValue valueWithCGPoint:ccp(0, -1)],
												  [NSValue valueWithCGPoint:ccp(0, 1)],
												  [NSValue valueWithCGPoint:ccp(1, -1)],
												  [NSValue valueWithCGPoint:ccp(1, 0)],
												  [NSValue valueWithCGPoint:ccp(1, 1)]],
								 @"attackPattern":@[[NSValue valueWithCGPoint:ccp(0, 1)],
													[NSValue valueWithCGPoint:ccp(0, -1)],
													[NSValue valueWithCGPoint:ccp(1, 0)],
													[NSValue valueWithCGPoint:ccp(-1, 0)],
													[NSValue valueWithCGPoint:ccp(0, -2)],
													[NSValue valueWithCGPoint:ccp(2, 0)],
													[NSValue valueWithCGPoint:ccp(0, 2)],
													[NSValue valueWithCGPoint:ccp(-2, 0)]]};
		[self.units setObject:wizard forKey:@(ZCUnitTypeWizard).description];
		
	}
										 
	return self;
}

@end
