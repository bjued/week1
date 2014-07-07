//
//  ZCData.h
//  zoneControl
//
//  Created by Brandon Jue on 6/18/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef enum {
	ZCUnitTypeKnightLance,
	ZCUnitTypeBarbarian,
	ZCUnitTypeKnightSword,
	ZCUnitTypeArcher,
	ZCUnitTypeWizard,
} ZCUnitType;

@interface ZCData : NSObject {
	
}
@property (nonatomic, strong) NSMutableDictionary *bases;
@property (nonatomic, strong) NSMutableDictionary *units;
@property (nonatomic, strong) NSMutableDictionary *flags;
BJ_SINGLETON_INTERFACE(ZCData, shared);

@end
