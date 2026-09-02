<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * Every tool driven once on a hit and once on a miss, as arguments.
 *
 * Two things read this and they need the same table: `ToolContractTest` holds
 * every answer to the schema its tool declares, and `bin/cli tools:record`
 * writes down what a filled answer looks like. A second table would drift, and
 * the recording would then illustrate calls nothing validates. It lives in
 * `Upkeep` rather than in `tests/` because a command may not depend on a test
 * class. The calls that reach a host outside this repository are here rather
 * than behind a skip list, because a host that does not answer is an answer the
 * schema declares — `D-DOC-008`.
 */
final class ToolCalls
{
    /**
     * The offered tools this table leaves out, each with why, in the words a
     * reader of the documentation is given them in.
     *
     * It is data rather than a paragraph in this file because two readers need
     * it and neither of them opens this class. `ToolSurface` states the absence
     * at the tool somebody is standing at in `documentation/server/tools/`,
     * where a missing link otherwise renders as nothing at all, and
     * `ToolAnswers` says the same on the map of the recording. `ToolAnswersTest`
     * holds this list against the table, so the next tool to drop out has to
     * arrive here with a reason before the suite goes green again — which is
     * the whole of what the prose could not do.
     *
     * @return array<string, string>
     */
    public static function undriven(): array
    {
        return [
            'typo3_feedback_record' => 'it is the one tool here that writes, and this table has two drivers '
                . 'rather than one. A call recorded from it would file a real feedback into the open ones every '
                . 'time ``ToolContractTest`` runs, not only when the recording does.',
            'typo3_feedback_list' => 'it answers with the feedback somebody else wrote, which is different in '
                . 'every checkout and carries the tool names that were current when each feedback was filed. '
                . 'One recorded title ends in a tool name cut to length, which reads to ``ToolNamingTest`` as a '
                . 'tool this server does not have.',
        ];
    }

    /**
     * The tools whose answered half is derived rather than recorded.
     *
     * A recording is evidence because the answer belongs to an installation
     * nobody else has. These eight read none: what reaches their answer is
     * `knowledge/` and two declarations about the root — that it is the core
     * monorepo, and which TYPO3 major that is. So the answer is the same for
     * every caller on that major, which makes it derivable, and what is
     * derivable is checked rather than believed.
     *
     * Measured on 2026-08-04 against `.checkouts/14.3` and against a root
     * holding nothing but those two declarations: these eight came back
     * byte-identical over their 20 calls, and every other tool moved.
     * `typo3_translation_domain_lookup` is the near miss and belongs to the
     * recorded half — it prints the installation's exact version into its text,
     * so a derived page would state a patch level no checkout here has.
     *
     * @return list<string>
     */
    public static function derived(): array
    {
        return [
            'typo3_commit_message_guide',
            'typo3_hint_lookup',
            'typo3_reference_list',
            'typo3_rule_lookup',
            'typo3_script_lookup',
            'typo3_system_extension_lookup',
            'typo3_task_guide',
            'typo3_test_run_guide',
        ];
    }

    /**
     * The calls, keyed by what each one is an example of.
     *
     * An installation-backed tool answers from whatever the caller is standing
     * in: nothing in a test run, so the entry exercises the unsupported path
     * there, and the packages of a core checkout when `tools:record` is pointed
     * at one. Both are worth seeing and neither is named in the key, because
     * the key describes the call rather than the answer.
     *
     * @return array<string, array{0: string, 1: array<string, mixed>}>
     */
    public static function all(): array
    {
        return [
            'scope' => ['typo3_server_scope', []],
            // The two forms side by side, because what the second one is for is
            // the difference between them: the same call asking only whether an
            // installation and its console can be reached.
            'scope: one section' => ['typo3_server_scope', ['sections' => ['installation']]],
            'rules: hit' => ['typo3_rule_lookup', ['query' => 'deprecation']],
            'rules: miss' => ['typo3_rule_lookup', ['query' => 'quantum entanglement pineapple']],
            'scripts: hit' => ['typo3_script_lookup', ['task' => 'functional tests']],
            'scripts: miss' => ['typo3_script_lookup', ['task' => 'quantum entanglement pineapple']],
            'brief: with a path' => ['typo3_task_guide', [
                'task' => 'Deprecate a public method',
                'paths' => ['typo3/sysext/core/Classes/Utility/GeneralUtility.php'],
                'changeType' => 'cleanup',
            ]],
            'brief: task only' => ['typo3_task_guide', ['task' => 'Add a badge to the list module']],
            'brief: paths of two kinds' => ['typo3_task_guide', [
                'task' => 'Fix the query that reads the events',
                'paths' => [
                    'packages/acme_events/Classes/Domain/Repository/EventRepository.php',
                    'typo3/sysext/core/Classes/Database/Query/QueryBuilder.php',
                ],
                'changeType' => 'bugfix',
            ]],
            'runTests: all' => ['typo3_test_run_guide', []],
            'runTests: hit' => ['typo3_test_run_guide', ['query' => 'phpstan']],
            'runTests: miss' => ['typo3_test_run_guide', ['query' => 'quantumflux']],
            'runTests: narrowed by paths' => ['typo3_test_run_guide', [
                'query' => 'what do I have to run',
                'paths' => ['Build/Sources/Sass/component/_card.scss'],
            ]],
            'hints: path' => ['typo3_hint_lookup', ['paths' => ['typo3/sysext/core/Classes/DataHandling/DataHandler.php']]],
            'hints: topic' => ['typo3_hint_lookup', ['task' => 'sass build']],
            'hints: miss' => ['typo3_hint_lookup', ['task' => 'quantumflux']],
            // The second call is the first one's answer handed back: that is
            // the two-step the tool documents, and a URL invented here would
            // illustrate a flow no caller has.
            'forge: what an issue says and what was decided' => ['typo3_forge_lookup', [
                'issue' => '110348',
            ]],
            // A report about rendering, whose evidence is in seven screenshots
            // and whose comments are the filenames of them.
            'forge: an issue whose evidence hangs off it' => ['typo3_forge_lookup', [
                'issue' => '88556',
            ]],
            // Half of this journal is a review bot pinging the tracker, which
            // is what a session sweeping candidates is paying for.
            'forge: an issue without the patch-set pings' => ['typo3_forge_lookup', [
                'issue' => '14858',
                'notes' => 'people',
            ]],
            'forge: no such issue' => ['typo3_forge_lookup', [
                'issue' => '99999999',
            ]],
            'forge: which other issues describe this' => ['typo3_forge_lookup', [
                'query' => 'cache busting',
                'limit' => 3,
            ]],
            'forge: nothing matches these words' => ['typo3_forge_lookup', [
                'query' => 'quantumflux transponder',
            ]],
            // Both of the last two are class names and the tracker knows one of
            // them, which is what says the miss reads the words rather than
            // advising about them (`D-ANS-038`).
            'forge: which of the words emptied the answer' => ['typo3_forge_lookup', [
                'query' => 'file renderer RendererRegistry FileRendererInterface',
            ]],
            'forge: the oldest issues nobody has resolved' => ['typo3_forge_lookup', [
                'backlog' => 'oldest',
                'limit' => 3,
            ]],
            'forge: what is known about one area' => ['typo3_forge_lookup', [
                'backlog' => 'stale',
                'category' => 'rte',
                'tracker' => 'Bug',
                'limit' => 3,
            ]],
            'forge: a word that names no area' => ['typo3_forge_lookup', [
                'backlog' => 'oldest',
                'category' => 'quantumflux',
            ]],
            // The question the full-text search reads as nine issues and the
            // tracker answers with six hundred.
            'forge: what one person has filed' => ['typo3_forge_lookup', [
                'backlog' => 'oldest',
                'reportedBy' => 'Frank Nägler',
                'status' => 'all',
                'limit' => 3,
            ]],
            'forge: a name naming more than one person' => ['typo3_forge_lookup', [
                'backlog' => 'oldest',
                'assignedTo' => 'daniel',
            ]],
            // What somebody says out loud, which the tracker cannot be asked:
            // it ANDs its filters, so the union is two reads and a merge.
            'forge: everything one person has touched' => ['typo3_forge_lookup', [
                'backlog' => 'stale',
                'involving' => 'Frank Nägler',
                'limit' => 3,
            ]],
            // The answer to a set of 621 that no page and no other word
            // reaches.
            'forge: the shape of one person\'s history' => ['typo3_forge_lookup', [
                'backlog' => 'oldest',
                'involving' => 'Frank Nägler',
                'status' => 'all',
                'breakdown' => true,
            ]],
            'gerrit: has this issue a patch already' => ['typo3_gerrit_lookup', [
                'issue' => '110348',
                'limit' => 3,
            ]],
            'gerrit: one change by number' => ['typo3_gerrit_lookup', [
                'change' => '89011',
            ]],
            // The head of a stack, which answers like a change standing alone
            // until the relation chain is in it — `D-ANS-094`.
            'gerrit: a change that is one part of a stack' => ['typo3_gerrit_lookup', [
                'change' => '91563',
            ]],
            // The shortlist two sessions built by hand on one day, in the four
            // filters they each scored 855 changes on — `D-ANS-107`.
            'gerrit: the open review backlog' => ['typo3_gerrit_lookup', [
                'backlog' => 'oldest',
                'maxSize' => 60,
                'minCodeReview' => 1,
                'negativeVotes' => false,
                'mergeable' => true,
                'limit' => 3,
            ]],
            'documentation: search' => ['typo3_documentation_lookup', [
                'queries' => ['page title event', 'page title provider'],
                'targetVersion' => '14.3',
                'limit' => 3,
            ]],
            'documentation: page' => ['typo3_documentation_lookup', [
                'page' => 'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html',
                'targetVersion' => '14.3',
            ]],
            'documentation: unsupported version' => ['typo3_documentation_lookup', [
                'queries' => ['page title event'],
                'targetVersion' => '999',
            ]],
            // The three shapes of the reporting session's own question, in one
            // call: an identifier that resolves, one it guessed wrong, and the
            // dead URL the guess came from — `D-ANS-118`.
            'permalink: identifiers and a URL at once' => ['typo3_permalink_lookup', [
                'identifiers' => ['t3tca:columns-onchange', 'typo3-cms-lowlevel:start'],
                'urls' => ['https://docs.typo3.org/m/typo3/reference-tca/11.5/en-us/Columns/Properties/OnChange.html'],
                'targetVersion' => '14.3',
            ]],
            'permalink: no manual is known for the shortcode' => ['typo3_permalink_lookup', [
                'identifiers' => ['quantumflux:start'],
                'targetVersion' => '14.3',
            ]],
            'permalink: unsupported version' => ['typo3_permalink_lookup', [
                'identifiers' => ['t3coreapi:start'],
                'targetVersion' => '999',
            ]],
            'components: list' => ['typo3_component_lookup', []],
            'components: hit' => ['typo3_component_lookup', ['query' => 'badge']],
            'components: miss' => ['typo3_component_lookup', ['query' => 'quantumflux']],
            'system extensions: hit' => ['typo3_system_extension_lookup', ['query' => 'impexp']],
            'system extensions: miss' => ['typo3_system_extension_lookup', ['query' => 'typo3/cms-content-blocks']],
            'system extensions: everything' => ['typo3_system_extension_lookup', []],
            'references' => ['typo3_reference_list', []],
            'domain: EXT reference' => ['typo3_translation_domain_lookup', [
                'path' => 'EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf',
            ]],
            'domain: checkout path' => ['typo3_translation_domain_lookup', [
                'path' => 'typo3/sysext/core/Resources/Private/Language/locallang.xlf',
            ]],
            'domain: on an older target' => ['typo3_translation_domain_lookup', [
                'path' => 'EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf',
                'targetVersion' => '13.4',
            ]],
            'domain: miss' => ['typo3_translation_domain_lookup', ['path' => 'somewhere/else.xlf']],
            'labels: hit' => ['typo3_label_lookup', ['query' => 'save']],
            'labels: miss' => ['typo3_label_lookup', ['query' => 'quantumflux']],
            'icons: hit' => ['typo3_icon_lookup', ['query' => 'actions-open']],
            'icons: several validated at once' => ['typo3_icon_lookup', [
                'identifiers' => ['actions-open', 'actions-cog', 'acme-events-teaser'],
            ]],
            'icons: everything' => ['typo3_icon_lookup', []],
            'modules' => ['typo3_backend_module_lookup', []],
            'namespaces' => ['typo3_fluid_namespace_list', []],
            'configuration' => ['typo3_configuration_lookup', ['path' => 'SYS/fluid']],
            'schema: one table' => ['typo3_schema_lookup', ['table' => 'tt_content']],
            'schema: every table' => ['typo3_schema_lookup', []],
            // The four states: a table of this project's own, the same one
            // counted rather than read, one the boundary refuses, and the list
            // of what it will read.
            'records: a table of this project' => ['typo3_record_lookup', ['table' => 'tx_acme_events_event']],
            'records: counted rather than read' => ['typo3_record_lookup', [
                'table' => 'tx_acme_events_event',
                'count' => true,
            ]],
            'records: a table it will not read' => ['typo3_record_lookup', ['table' => 'tt_content']],
            'records: what it reads' => ['typo3_record_lookup', []],
            'services: by class' => ['typo3_service_lookup', ['query' => 'PageRenderer']],
            'services: by tag' => ['typo3_service_lookup', ['tag' => 'event.listener', 'limit' => 3]],
            // The record is what decides which structure applies, so the two
            // calls are one with it and one about a column that is not flex.
            'flexform: the structure a content element resolves to' => ['typo3_flexform_lookup', [
                'table' => 'tt_content',
                'field' => 'pi_flexform',
                'record' => ['CType' => 'acme_events_teaser'],
            ]],
            'flexform: a column that is not one' => ['typo3_flexform_lookup', [
                'table' => 'tt_content',
                'field' => 'bodytext',
            ]],
            'changelog: hit' => ['typo3_changelog_lookup', ['query' => 'ext_tables.php']],
            'changelog: swept by tag' => ['typo3_changelog_lookup', ['type' => 'deprecation', 'tag' => 'FullyScanned']],
            'changelog: miss' => ['typo3_changelog_lookup', ['query' => 'quantumflux']],
            'ter: what is published under a key' => ['typo3_ter_lookup', [
                'extension' => 'blog',
                'limit' => 3,
            ]],
            // The release audit's own question, in the form it is asked: the
            // number ext_emconf.php names, held against what is published.
            'ter: is this version already out' => ['typo3_ter_lookup', [
                'extension' => 'blog',
                'extensionVersion' => '14.0.1',
                'limit' => 3,
            ]],
            'ter: nothing is published under this key' => ['typo3_ter_lookup', [
                'extension' => 'quantumflux_transponder',
            ]],
            // The name of the wrong kind, which the registry answers 400 to and
            // this answers without a read.
            'ter: a composer package name' => ['typo3_ter_lookup', [
                'extension' => 'georgringer/news',
            ]],
            'project' => ['typo3_project_describe', []],
            'extension' => ['typo3_extension_describe', ['extension' => 'backend']],
            'catalog scope' => ['typo3_snapshot_scope', []],
            'commit: from parts' => ['typo3_commit_message_guide', [
                'keyword' => 'BUGFIX',
                'summary' => 'Show hidden records in the import preview',
                'issue' => '106123',
            ]],
            'commit: from a message' => ['typo3_commit_message_guide', [
                'message' => "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main",
            ]],
        ];
    }
}
