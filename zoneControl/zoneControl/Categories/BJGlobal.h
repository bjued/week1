//
//  KAGlobal.h
//  warlords
//
//  Created by Brandon Jue on 3/9/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import <Foundation/Foundation.h>

#define BJ_SINGLETON_INTERFACE(classname, accessorMethodName) \
+ (classname *)accessorMethodName;
#define BJ_SINGLETON_IMPLEMENTATION(classname, accessorMethodName) \
static classname *accessorMethodName = nil; \
+ (void)initialize { \
static BOOL initialized = NO; \
if (!initialized) { \
initialized = YES; \
accessorMethodName = [[self alloc] init]; \
} \
} \
+ (id)accessorMethodName { \
return accessorMethodName; \
}

@interface BJGlobal : NSObject

@end
