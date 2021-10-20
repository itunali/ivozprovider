import SettingsApplications from '@mui/icons-material/SettingsApplications';
import EntityInterface from 'lib/entities/EntityInterface';
import _ from 'lib/services/translations/translate';
import defaultEntityBehavior from 'lib/entities/DefaultEntityBehavior';
import Form from './Form'
import EntityService from 'lib/services/entity/EntityService';
import genericForeignKeyResolver from 'lib/services/api/genericForeigKeyResolver';
import entities from '../index';
import { UserProperties, UserPropertiesList } from './UserProperties';

const properties: UserProperties = {
    'name': {
        label: _('Name'),
    },
    'lastname': {
        label: _('Lastname'),
    },
    'email': {
        label: _('Email'),
        helpText: _('Used as voicemail reception and user portal credential'),
    },
    'pass': {
        label: _('Password'),
    },
    'active': {
        label: _('Active'),
        enum: {
            '0': _('No'),
            '1': _('Yes'),
        },
        visualToggle: {
            '0': {
                show: [],
                hide: ['pass'],
            },
            '1': {
                show: ['pass'],
                hide: [],
            }
        }
    },
    'timezone': {
        label: _('Timezone'),
    },
    'transformationRuleSet': {
        label: _('Numeric transformation'),
    },
    'terminal': {
        label: _('Terminal'),
    },
    /*'statusIcon': _('Status'),*/
    'extension': {
        label: _('Screen Extension'),
        null: _('Unassigned'),
    },
    'outgoingDdi': {
        label: _('Outgoing DDI'),
    },
    'outgoingDdiRule': {
        label: _('Outgoing DDI Rule'),
        helpText: _('Rules to manipulate outgoingDDI when user directly calls to external numbers.'),
    },
    'callAcl': {
        label: _('Call ACL'),
    },
    'doNotDisturb': {
        label: _('Do not disturb'),
    },
    'isBoss': {
        label: _('Is boss'),
        enum: {
            '0': _('No'),
            '1': _('Yes'),
        },
        visualToggle: {
            '0': {
                show: [],
                hide: ['bossAssistant', 'bossAssistantWhiteList'],
            },
            '1': {
                show: ['bossAssistant', 'bossAssistantWhiteList'],
                hide: [],
            }
        }
    },
    'bossAssistant': {
        label: _('Assistant'),
    },
    'bossAssistantWhiteList': {
        label: _('Boss Whitelist'),
        helpText: _('Origins matching this list will call directly to the user.'),
    },
    'maxCalls': {
        label: _('Call waiting'),
        helpText: _('Limits received calls when already handling this number of calls. Set 0 for unlimited.'),
    },
    'voicemailEnabled': {
        label: _('Voicemail enabled'),
        enum: {
            '0': _('No'),
            '1': _('Yes'),
        },
        visualToggle: {
            '0': {
                show: [],
                hide: ['voicemailSendMail', 'voicemailAttachSound', 'voicemailLocution'],
            },
            '1': {
                show: ['voicemailSendMail', 'voicemailAttachSound', 'voicemailLocution'],
                hide: [],
            }
        }
    },
    'voicemailLocution': {
        label: _('Voicemail Locution'),
    },
    'voicemailSendMail': {
        label: _('Voicemail send mail'),
        enum: {
            '0': _('No'),
            '1': _('Yes'),
        },
        visualToggle: {
            '0': {
                hide: ['voicemailAttachSound'],
                show: [],
            },
            '1': {
                show: ['voicemailAttachSound'],
                hide: [],
            }
        }
    },
    'voicemailAttachSound': {
        label: _('Voicemail attach sound'),
    },
    'pickupGroupIds': {
        label: _('Pick Up Groups'),
    },
    'language': {
        label: _('Language'),
    },
    'externalIpCalls': {
        label: _('Calls from non-granted IPs'),
        helpText: _("Enable calling from non-granted IP addresses for this user. It limits the number of outgoing calls to avoid toll-fraud. 'None' value makes outgoing calls unlimited as long as company IP policy is fulfilled."),
    },
    'rejectCallMethod': {
        label: _('Call rejection method'),
    },
    'gsQRCode': {
        label: _('QR Code'),
        helpText: _('Add QR Code to user portal to provision GS Wave mobile softphone'),
    },
    'multiContact': {
        label: _('Multi contact'),
        helpText: _("Set to 'No' to call only to latest registered SIP device instead of making all registered devices ring."),
        enum: {
            '0': _('No'),
            '1': _('Yes'),
        },
        visualToggle: {
            '0': {
                show: [],
                hide: ['rejectCallMethod'],
            },
            '1': {
                show: ['rejectCallMethod'],
                hide: [],
            },
        }
    }
};

async function foreignKeyResolver(data: UserPropertiesList, entityService: EntityService) {
    const promises = [];
    const { Ddi, Extension, Terminal } = entities;

    promises.push(
        genericForeignKeyResolver(
            data,
            'terminal',
            Terminal.path,
            Terminal.toStr
        )
    );

    promises.push(
        genericForeignKeyResolver(
            data,
            'extension',
            Extension.path,
            Extension.toStr,
        )
    );

    promises.push(
        genericForeignKeyResolver(
            data,
            'outgoingDdi',
            Ddi.path,
            Ddi.toStr,
        )
    );

    await Promise.all(promises);

    return data;
}

const user: EntityInterface = {
    ...defaultEntityBehavior,
    icon: <SettingsApplications />,
    iden: 'User',
    title: _('User', { count: 2 }),
    path: '/users',
    toStr: (row: any) => `${row.name} ${row.lastname}`,
    properties,
    Form,
    foreignKeyResolver
};

export default user;